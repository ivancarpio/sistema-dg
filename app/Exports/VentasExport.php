<?php

namespace App\Exports;

use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected $ventas;
    protected $filtros;
    protected $totales;

    public function __construct($ventas, $filtros = [], $totales = [])
    {
        $this->ventas = $ventas;

        if ($filtros instanceof \Carbon\Carbon || is_string($filtros)) {
            $this->filtros = ['periodo_label' => (string)$filtros];
        } else {
            $this->filtros = is_array($filtros) ? $filtros : [];
        }

        if (is_array($totales) && !empty($totales)) {
            $this->totales = $totales;
        } else {
            $totEf = 0; $totQr = 0; $totPorPagar = 0;
            foreach ($this->ventas as $v) {
                $cobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
                if ($v->metodo_pago === 'Efectivo') $totEf += $cobrado;
                elseif ($v->metodo_pago === 'QR') $totQr += $cobrado;
                elseif ($v->metodo_pago === 'Por Pagar') {
                    $totPorPagar += floatval($v->saldo_pendiente ?? 0);
                    if ($v->metodo_adelanto === 'QR') $totQr += $cobrado;
                    else $totEf += $cobrado;
                }
            }
            $this->totales = [
                'efectivo'  => $totEf,
                'qr'        => $totQr,
                'por_pagar' => $totPorPagar,
                'general'   => $this->ventas->sum('total'),
            ];
        }
    }

    public function view(): View
    {
        return view('reportes.excel', [
            'ventas'  => $this->ventas,
            'filtros' => $this->filtros,
            'totales' => $this->totales,
        ]);
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Fila 1: título grande
            1 => [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
            // Fila 3: encabezados de columna
            3 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
