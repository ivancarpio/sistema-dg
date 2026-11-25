<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    use HasFactory;

    protected $table = 'tipos_cambio';

    protected $fillable = [
        'moneda_origen',
        'moneda_destino',
        'tasa_venta',
        'tasa_compra',
        'fuente',
        'consultado_en',
        'user_id',
        'observacion',
    ];

    protected $casts = [
        'tasa_venta' => 'decimal:4',
        'tasa_compra' => 'decimal:4',
        'consultado_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para obtener el registro más reciente
     */
    public function scopeUltimo($query, $origen = 'BOB', $destino = 'PEN')
    {
        return $query->where('moneda_origen', $origen)
                     ->where('moneda_destino', $destino)
                     ->orderBy('consultado_en', 'desc')
                     ->first();
    }
}
