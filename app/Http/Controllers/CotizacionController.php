<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Producto;
use App\Services\TipoCambioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    /**
     * Listado principal e historial de cotizaciones con KPIs y filtros.
     */
    public function index(Request $request)
    {
        $query = Cotizacion::with(['detalles', 'cliente', 'usuario']);

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->where('numero_cotizacion', 'LIKE', "%{$term}%")
                  ->orWhere('destinatario_nombre', 'LIKE', "%{$term}%")
                  ->orWhere('destinatario_empresa', 'LIKE', "%{$term}%")
                  ->orWhere('destinatario_telefono', 'LIKE', "%{$term}%")
                  ->orWhere('destinatario_nit', 'LIKE', "%{$term}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_cotizacion', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $cotizaciones = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // KPIs del mes actual
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $totalMes = Cotizacion::whereBetween('fecha', [$inicioMes, $finMes])->count();
        $pendientesCount = Cotizacion::where('estado', 'pendiente')->count();
        $enviadasAprobadasCount = Cotizacion::whereIn('estado', ['enviada', 'aprobada'])
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->count();
        $montoCotizadoMes = Cotizacion::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', '!=', 'rechazada')
            ->sum('total');

        $empresa = $this->datosEmpresa();
        $tipoCambio = app(TipoCambioService::class)->actual();

        return view('cotizaciones.index', compact(
            'cotizaciones',
            'totalMes',
            'pendientesCount',
            'enviadasAprobadasCount',
            'montoCotizadoMes',
            'empresa',
            'tipoCambio'
        ));
    }

    /**
     * Constructor interactivo para crear una nueva cotización.
     */
    public function create()
    {
        $productos = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria'])
            ->orderBy('nombre', 'asc')
            ->get();

        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre', 'asc')->get();
        $tipoCambio = app(TipoCambioService::class)->actual();
        $numeroSugerido = $this->generarNumeroCotizacion();

        return view('cotizaciones.form', [
            'cotizacion' => null,
            'itemsIniciales' => [],
            'productos' => $productos,
            'categorias' => $categorias,
            'clientes' => $clientes,
            'tipoCambio' => $tipoCambio,
            'numeroSugerido' => $numeroSugerido,
        ]);
    }

    /**
     * Guarda una nueva cotización y sus ítems.
     */
    public function store(Request $request)
    {
        $validated = $this->validarSolicitud($request);

        try {
            $cotizacion = DB::transaction(function () use ($request, $validated) {
                $numero = trim($request->input('numero_cotizacion') ?: '');
                if ($numero === '' || Cotizacion::where('numero_cotizacion', $numero)->exists()) {
                    $numero = $this->generarNumeroCotizacion();
                }

                $moneda = $validated['moneda'];
                $simbolo = $moneda === 'PEN' ? 'S/' : 'Bs.';
                $tasaAplicada = floatval($request->input('tasa_cambio_aplicada', 0));
                if ($tasaAplicada <= 0) {
                    $tc = app(TipoCambioService::class)->actual();
                    $tasaAplicada = floatval($tc->tasa_venta ?? 3.6);
                }

                $descuento = max(0, floatval($request->input('descuento', 0)));
                $subtotal = 0;

                foreach ($validated['items'] as $item) {
                    $cant = floatval($item['cantidad']);
                    $pu = floatval($item['precio_unitario']);
                    $subtotal += round($cant * $pu, 2);
                }

                $descuento = min($descuento, $subtotal);
                $total = max(0, round($subtotal - $descuento, 2));

                $cotizacion = Cotizacion::create([
                    'numero_cotizacion'     => $numero,
                    'fecha'                 => $request->filled('fecha') ? Carbon::parse($request->fecha) : now(),
                    'valida_hasta'          => $request->filled('valida_hasta') ? Carbon::parse($request->valida_hasta)->toDateString() : now()->addDays(7)->toDateString(),
                    'tipo_cotizacion'       => $validated['tipo_cotizacion'],
                    'id_cliente'            => $request->filled('id_cliente') ? intval($request->id_cliente) : null,
                    'destinatario_nombre'   => trim($validated['destinatario_nombre']),
                    'destinatario_empresa'  => $request->filled('destinatario_empresa') ? trim($request->destinatario_empresa) : null,
                    'destinatario_nit'      => $request->filled('destinatario_nit') ? trim($request->destinatario_nit) : null,
                    'destinatario_telefono' => $request->filled('destinatario_telefono') ? trim($request->destinatario_telefono) : null,
                    'destinatario_direccion'=> $request->filled('destinatario_direccion') ? trim($request->destinatario_direccion) : null,
                    'moneda'                => $moneda,
                    'simbolo_moneda'        => $simbolo,
                    'tasa_cambio_aplicada'  => $tasaAplicada,
                    'mostrar_equivalente'   => $request->has('mostrar_equivalente') ? ($request->boolean('mostrar_equivalente')) : true,
                    'subtotal'              => $subtotal,
                    'descuento'             => $descuento,
                    'total'                 => $total,
                    'condiciones'           => $request->filled('condiciones') ? trim($request->condiciones) : null,
                    'observaciones'         => $request->filled('observaciones') ? trim($request->observaciones) : null,
                    'estado'                => $request->input('accion_post') === 'whatsapp' ? 'enviada' : 'pendiente',
                    'id_usuario'            => Auth::id() ?? 1,
                ]);

                $this->guardarDetalles($cotizacion, $validated['items']);

                return $cotizacion;
            });

            return $this->redirigirSegunAccion($cotizacion, $request->input('accion_post', 'guardar'), '¡Cotización ' . $cotizacion->numero_cotizacion . ' creada exitosamente!');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo guardar la cotización: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario de edición para modificar una cotización existente.
     */
    public function edit($id)
    {
        $cotizacion = Cotizacion::with('detalles')->findOrFail($id);

        $productos = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria'])
            ->orderBy('nombre', 'asc')
            ->get();

        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre', 'asc')->get();
        $tipoCambio = app(TipoCambioService::class)->actual();

        $itemsIniciales = $cotizacion->detalles->map(function ($d) {
            return [
                'key' => $d->id_producto ? ('prod_' . $d->id_producto . '_' . uniqid()) : ('ext_' . $d->id),
                'id_producto' => $d->id_producto,
                'codigo' => $d->codigo_producto ?? '',
                'nombre' => $d->nombre_producto,
                'unidad' => $d->unidad_medida ?? 'Pieza',
                'es_externo' => (bool) $d->es_externo,
                'cantidad' => floatval($d->cantidad),
                'precio_unitario' => floatval($d->precio_unitario),
            ];
        })->values()->all();

        return view('cotizaciones.form', [
            'cotizacion' => $cotizacion,
            'itemsIniciales' => $itemsIniciales,
            'productos' => $productos,
            'categorias' => $categorias,
            'clientes' => $clientes,
            'tipoCambio' => $tipoCambio,
            'numeroSugerido' => $cotizacion->numero_cotizacion,
        ]);
    }

    /**
     * Actualiza una cotización existente.
     */
    public function update(Request $request, $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $validated = $this->validarSolicitud($request);

        try {
            DB::transaction(function () use ($request, $validated, $cotizacion) {
                $moneda = $validated['moneda'];
                $simbolo = $moneda === 'PEN' ? 'S/' : 'Bs.';
                $tasaAplicada = floatval($request->input('tasa_cambio_aplicada', $cotizacion->tasa_cambio_aplicada ?? 3.6));

                $descuento = max(0, floatval($request->input('descuento', 0)));
                $subtotal = 0;

                foreach ($validated['items'] as $item) {
                    $cant = floatval($item['cantidad']);
                    $pu = floatval($item['precio_unitario']);
                    $subtotal += round($cant * $pu, 2);
                }

                $descuento = min($descuento, $subtotal);
                $total = max(0, round($subtotal - $descuento, 2));

                $cotizacion->update([
                    'valida_hasta'          => $request->filled('valida_hasta') ? Carbon::parse($request->valida_hasta)->toDateString() : $cotizacion->valida_hasta,
                    'tipo_cotizacion'       => $validated['tipo_cotizacion'],
                    'id_cliente'            => $request->filled('id_cliente') ? intval($request->id_cliente) : null,
                    'destinatario_nombre'   => trim($validated['destinatario_nombre']),
                    'destinatario_empresa'  => $request->filled('destinatario_empresa') ? trim($request->destinatario_empresa) : null,
                    'destinatario_nit'      => $request->filled('destinatario_nit') ? trim($request->destinatario_nit) : null,
                    'destinatario_telefono' => $request->filled('destinatario_telefono') ? trim($request->destinatario_telefono) : null,
                    'destinatario_direccion'=> $request->filled('destinatario_direccion') ? trim($request->destinatario_direccion) : null,
                    'moneda'                => $moneda,
                    'simbolo_moneda'        => $simbolo,
                    'tasa_cambio_aplicada'  => $tasaAplicada,
                    'mostrar_equivalente'   => $request->has('mostrar_equivalente') ? ($request->boolean('mostrar_equivalente')) : false,
                    'subtotal'              => $subtotal,
                    'descuento'             => $descuento,
                    'total'                 => $total,
                    'condiciones'           => $request->filled('condiciones') ? trim($request->condiciones) : null,
                    'observaciones'         => $request->filled('observaciones') ? trim($request->observaciones) : null,
                ]);

                $cotizacion->detalles()->delete();
                $this->guardarDetalles($cotizacion, $validated['items']);
            });

            return $this->redirigirSegunAccion($cotizacion, $request->input('accion_post', 'guardar'), '¡Cotización ' . $cotizacion->numero_cotizacion . ' actualizada con éxito!');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo actualizar la cotización: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detalle completo en JSON para modales de vista rápida y WhatsApp.
     */
    public function show($id)
    {
        $cotizacion = Cotizacion::with(['detalles', 'cliente', 'usuario'])->findOrFail($id);
        $empresa = $this->datosEmpresa();
        $mensajeWa = $cotizacion->construirMensajeWhatsApp($empresa);
        $telefonoWa = $cotizacion->telefonoWhatsApp();
        $equiv = $cotizacion->convertirAOtraMoneda();

        return response()->json([
            'success'               => true,
            'id'                    => $cotizacion->id,
            'numero_cotizacion'     => $cotizacion->numero_cotizacion,
            'fecha'                 => $cotizacion->fecha ? $cotizacion->fecha->format('d/m/Y H:i') : '',
            'valida_hasta'          => $cotizacion->valida_hasta ? $cotizacion->valida_hasta->format('d/m/Y') : 'Sin vencimiento',
            'tipo_cotizacion'       => $cotizacion->tipo_cotizacion,
            'tipo_label'            => $cotizacion->tipo_cotizacion === 'tienda_externa' ? 'Tienda Externa' : 'Cliente',
            'destinatario_nombre'   => $cotizacion->destinatario_nombre,
            'destinatario_empresa'  => $cotizacion->destinatario_empresa,
            'destinatario_nit'      => $cotizacion->destinatario_nit,
            'destinatario_telefono' => $cotizacion->destinatario_telefono,
            'destinatario_direccion'=> $cotizacion->destinatario_direccion,
            'moneda'                => $cotizacion->moneda,
            'simbolo_moneda'        => $cotizacion->simbolo_moneda,
            'tasa_cambio_aplicada'  => $cotizacion->tasa_cambio_aplicada ? floatval($cotizacion->tasa_cambio_aplicada) : null,
            'mostrar_equivalente'   => (bool) $cotizacion->mostrar_equivalente,
            'equivalente_monto'     => $equiv,
            'equivalente_simbolo'   => $cotizacion->simboloOtraMoneda(),
            'subtotal'              => floatval($cotizacion->subtotal),
            'descuento'             => floatval($cotizacion->descuento),
            'total'                 => floatval($cotizacion->total),
            'condiciones'           => $cotizacion->condiciones,
            'observaciones'         => $cotizacion->observaciones,
            'estado'                => $cotizacion->estado,
            'usuario'               => optional($cotizacion->usuario)->name ?? 'Usuario',
            'telefono_wa'           => $telefonoWa,
            'mensaje_wa'            => $mensajeWa,
            'pdf_url'               => route('cotizaciones.pdf', $cotizacion->id),
            'imprimir_url'          => route('cotizaciones.imprimir', $cotizacion->id),
            'detalles'              => $cotizacion->detalles->map(function ($d) {
                return [
                    'codigo'          => $d->codigo_producto ?: '—',
                    'nombre'          => $d->nombre_producto,
                    'unidad'          => $d->unidad_medida,
                    'es_externo'      => (bool) $d->es_externo,
                    'cantidad'        => floatval($d->cantidad),
                    'precio_unitario' => floatval($d->precio_unitario),
                    'subtotal'        => floatval($d->subtotal),
                ];
            }),
        ]);
    }

    /**
     * Cambia el estado de una cotización.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,enviada,aprobada,rechazada,vencida',
        ]);

        $cotizacion = Cotizacion::findOrFail($id);
        $cotizacion->estado = $request->estado;
        $cotizacion->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'estado'  => $cotizacion->estado,
                'message' => 'Estado actualizado.',
            ]);
        }

        return back()->with('success', "Estado de {$cotizacion->numero_cotizacion} actualizado a " . ucfirst($cotizacion->estado) . '.');
    }

    /**
     * Elimina una cotización.
     */
    public function destroy($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $num = $cotizacion->numero_cotizacion;
        $cotizacion->delete();

        return redirect()->route('cotizaciones.index')->with('success', "Cotización {$num} eliminada correctamente.");
    }

    /**
     * Genera y transmite el PDF profesional con el logo oficial configurado.
     */
    public function pdf($id)
    {
        $cotizacion = Cotizacion::with(['detalles', 'cliente', 'usuario'])->findOrFail($id);
        $empresa = $this->datosEmpresa();
        $logoBase64 = $this->obtenerLogoBase64();

        $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion', 'empresa', 'logoBase64'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Cotizacion_' . $cotizacion->numero_cotizacion . '.pdf');
    }

    /**
     * Vista optimizada de impresión directa con logo oficial de empresa.
     */
    public function imprimir($id)
    {
        $cotizacion = Cotizacion::with(['detalles', 'cliente', 'usuario'])->findOrFail($id);
        $empresa = $this->datosEmpresa();
        $logoBase64 = $this->obtenerLogoBase64();

        return view('cotizaciones.imprimir', compact('cotizacion', 'empresa', 'logoBase64'));
    }

    // ========================================================
    // HELPERS INTERNOS PRIVADOS
    // ========================================================

    private function validarSolicitud(Request $request): array
    {
        return $request->validate([
            'tipo_cotizacion'         => 'required|in:cliente,tienda_externa',
            'destinatario_nombre'     => 'required|string|max:255',
            'destinatario_empresa'    => 'nullable|string|max:255',
            'destinatario_nit'        => 'nullable|string|max:50',
            'destinatario_telefono'   => 'nullable|string|max:50',
            'destinatario_direccion'  => 'nullable|string|max:255',
            'moneda'                  => 'required|in:BOB,PEN',
            'items'                   => 'required|array|min:1',
            'items.*.nombre'          => 'required|string|max:255',
            'items.*.cantidad'        => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.unidad'          => 'nullable|string|max:50',
            'items.*.codigo'          => 'nullable|string|max:50',
            'items.*.id_producto'     => 'nullable|integer',
            'items.*.es_externo'      => 'nullable',
        ], [
            'destinatario_nombre.required' => 'Ingresa el nombre del cliente o contacto de la tienda.',
            'items.required'               => 'Agrega al menos un producto o ítem a la cotización.',
            'items.min'                    => 'La cotización debe incluir al menos un producto.',
        ]);
    }

    private function guardarDetalles(Cotizacion $cotizacion, array $items): void
    {
        foreach ($items as $item) {
            $cant = floatval($item['cantidad']);
            $pu = floatval($item['precio_unitario']);
            $idProd = !empty($item['id_producto']) ? intval($item['id_producto']) : null;

            // Verificar que el producto aún exista si viene vinculado
            if ($idProd !== null && !Producto::where('id', $idProd)->exists()) {
                $idProd = null;
            }

            DetalleCotizacion::create([
                'id_cotizacion'   => $cotizacion->id,
                'id_producto'     => $idProd,
                'codigo_producto' => !empty($item['codigo']) ? trim($item['codigo']) : null,
                'nombre_producto' => trim($item['nombre']),
                'unidad_medida'   => !empty($item['unidad']) ? trim($item['unidad']) : 'Pieza',
                'es_externo'      => !empty($item['es_externo']) || $idProd === null,
                'cantidad'        => $cant,
                'precio_unitario' => $pu,
                'subtotal'        => round($cant * $pu, 2),
            ]);
        }
    }

    private function redirigirSegunAccion(Cotizacion $cotizacion, string $accion, string $mensaje)
    {
        $redirect = redirect()->route('cotizaciones.index')
            ->with('success', $mensaje)
            ->with('cotizacion_guardada_id', $cotizacion->id);

        if ($accion === 'pdf') {
            return $redirect->with('abrir_pdf_id', $cotizacion->id);
        }
        if ($accion === 'imprimir') {
            return $redirect->with('abrir_print_id', $cotizacion->id);
        }
        if ($accion === 'whatsapp') {
            return $redirect->with('abrir_wa_id', $cotizacion->id);
        }

        return $redirect->with('abrir_exito_id', $cotizacion->id);
    }

    private function generarNumeroCotizacion(): string
    {
        $ultimo = Cotizacion::orderBy('id', 'desc')->first();
        $siguiente = $ultimo ? ($ultimo->id + 1) : 1;

        do {
            $codigo = 'COT-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
            $existe = Cotizacion::where('numero_cotizacion', $codigo)->exists();
            if ($existe) {
                $siguiente++;
            }
        } while ($existe);

        return $codigo;
    }

    private function datosEmpresa(): array
    {
        return [
            'nombre'    => Configuracion::get('nombre_empresa', 'Distribuidora Guadalupe'),
            'nit'       => Configuracion::get('nit_empresa', ''),
            'telefono'  => Configuracion::get('telefono_empresa', ''),
            'direccion' => Configuracion::get('direccion_empresa', ''),
            'pie'       => Configuracion::get('pie_ticket', '¡Gracias por su preferencia!'),
        ];
    }

    /**
     * Obtiene el logo oficial en Base64 garantizando compatibilidad con DomPDF e Impresión,
     * sincronizado automáticamente con Configuración -> Actualizar Logo Oficial.
     */
    private function obtenerLogoBase64(): ?string
    {
        $rutas = [
            public_path('img/logo_white.png'),
            public_path('img/logo.png'),
            storage_path('app/public/img/logo_white.png'),
            storage_path('app/public/img/logo.png'),
        ];

        foreach ($rutas as $ruta) {
            if (file_exists($ruta) && is_readable($ruta)) {
                $data = @file_get_contents($ruta);
                if ($data !== false && strlen($data) > 0) {
                    $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
                    $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png';
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        }

        return null;
    }
}
