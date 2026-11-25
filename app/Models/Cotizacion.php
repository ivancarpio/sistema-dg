<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero_cotizacion',
        'fecha',
        'valida_hasta',
        'tipo_cotizacion',
        'id_cliente',
        'destinatario_nombre',
        'destinatario_empresa',
        'destinatario_nit',
        'destinatario_telefono',
        'destinatario_direccion',
        'moneda',
        'simbolo_moneda',
        'tasa_cambio_aplicada',
        'mostrar_equivalente',
        'subtotal',
        'descuento',
        'total',
        'condiciones',
        'observaciones',
        'estado',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'valida_hasta' => 'date',
        'tasa_cambio_aplicada' => 'decimal:4',
        'mostrar_equivalente' => 'boolean',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class, 'id_cotizacion');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Calcula el equivalente de un monto en la divisa opuesta usando la tasa de cambio guardada.
     */
    public function convertirAOtraMoneda(?float $monto = null): ?float
    {
        $monto = $monto ?? floatval($this->total);
        $tasa = floatval($this->tasa_cambio_aplicada ?? 0);
        if ($tasa <= 0) {
            return null;
        }

        if ($this->moneda === 'PEN') {
            return round($monto * $tasa, 2);
        }

        return round($monto / $tasa, 2);
    }

    public function simboloOtraMoneda(): string
    {
        return $this->moneda === 'PEN' ? 'Bs.' : 'S/';
    }

    /**
     * Formatea el número telefónico listo para https://wa.me/{numero}
     */
    public function telefonoWhatsApp(?string $codigoPaisDefecto = null): string
    {
        $limpio = preg_replace('/[^0-9]/', '', (string) $this->destinatario_telefono);
        if (empty($limpio)) {
            return '';
        }

        // Si ya incluye código internacional (ej. 519xxxxxxxx o 5917xxxxxxx)
        if (strlen($limpio) >= 10 && (str_starts_with($limpio, '51') || str_starts_with($limpio, '591'))) {
            return $limpio;
        }

        // Inferir si el número es de Perú (9 dígitos empezando en 9) o Bolivia (8 dígitos empezando en 6/7)
        if (strlen($limpio) === 9 && str_starts_with($limpio, '9')) {
            return '51' . $limpio;
        }
        if (strlen($limpio) === 8 && (str_starts_with($limpio, '6') || str_starts_with($limpio, '7'))) {
            return '591' . $limpio;
        }

        $prefijo = $codigoPaisDefecto ?: (Configuracion::paisOperacion() === 'PE' ? '51' : '591');
        return $prefijo . $limpio;
    }

    /**
     * Genera el mensaje formateado profesional para enviar por WhatsApp.
     */
    public function construirMensajeWhatsApp(array $empresa = []): string
    {
        $nombreEmpresa = $empresa['nombre'] ?? Configuracion::get('nombre_empresa', 'Distribuidora Guadalupe');
        $sim = $this->simbolo_moneda ?: ($this->moneda === 'PEN' ? 'S/' : 'Bs.');
        $fechaFmt = $this->fecha ? $this->fecha->format('d/m/Y') : now()->format('d/m/Y');
        $validaFmt = $this->valida_hasta ? $this->valida_hasta->format('d/m/Y') : null;

        $lineas = [];
        $lineas[] = "*{$nombreEmpresa}*";
        $lineas[] = "*COTIZACIÓN N° {$this->numero_cotizacion}*";
        $lineas[] = "Fecha: {$fechaFmt}" . ($validaFmt ? " | Válida hasta: {$validaFmt}" : "");
        $dest = $this->destinatario_empresa
            ? "{$this->destinatario_empresa} ({$this->destinatario_nombre})"
            : $this->destinatario_nombre;
        $lineas[] = ($this->tipo_cotizacion === 'tienda_externa' ? 'Tienda / Empresa: ' : 'Cliente: ') . $dest;
        $lineas[] = "";
        $lineas[] = "*DETALLE DE PRODUCTOS:*";

        foreach ($this->detalles as $idx => $det) {
            $cantFmt = fmod(floatval($det->cantidad), 1) == 0
                ? number_format($det->cantidad, 0)
                : number_format($det->cantidad, 2);
            $puFmt = number_format($det->precio_unitario, 2);
            $subFmt = number_format($det->subtotal, 2);
            $cod = $det->codigo_producto ? "[{$det->codigo_producto}] " : '';
            $lineas[] = ($idx + 1) . ". {$cod}{$det->nombre_producto}";
            $lineas[] = "   {$cantFmt} {$det->unidad_medida} x {$sim} {$puFmt} = *{$sim} {$subFmt}*";
        }

        $lineas[] = "";
        if (floatval($this->descuento) > 0) {
            $lineas[] = "Subtotal: {$sim} " . number_format($this->subtotal, 2);
            $lineas[] = "Descuento: -{$sim} " . number_format($this->descuento, 2);
        }
        $lineas[] = "*TOTAL COTIZADO: {$sim} " . number_format($this->total, 2) . "*";

        if ($this->mostrar_equivalente && ($equiv = $this->convertirAOtraMoneda()) !== null) {
            $simOtro = $this->simboloOtraMoneda();
            $tasa = number_format($this->tasa_cambio_aplicada, 2);
            $lineas[] = "Equivalente: *{$simOtro} " . number_format($equiv, 2) . "* (Tasa ref.: {$tasa})";
        }

        if ($this->condiciones) {
            $lineas[] = "";
            $lineas[] = "Condiciones: {$this->condiciones}";
        }
        if ($this->observaciones) {
            $lineas[] = "Nota: {$this->observaciones}";
        }

        $lineas[] = "";
        $lineas[] = "Quedamos atentos a su confirmación.";

        return implode("\n", $lineas);
    }
}
