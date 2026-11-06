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
        $this->ventas  = $ventas;
        $this->filtros = $filtros;
        $this->totales = $totales;
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
