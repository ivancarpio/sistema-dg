<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected $productos;
    protected $totales;
    protected $filtros;

    public function __construct($productos, array $totales = [], array $filtros = [])
    {
        $this->productos = $productos;
        $this->totales   = $totales;
        $this->filtros   = $filtros;
    }

    public function view(): View
    {
        return view('reportes.productos_excel', [
            'productos' => $this->productos,
            'totales'   => $this->totales,
            'filtros'   => $this->filtros,
            'fecha'     => now()->format('d/m/Y H:i'),
        ]);
    }

    public function title(): string
    {
        return 'Inventario y Catálogo';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0F172A']],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
            ],
            2 => [
                'font' => ['size' => 9, 'color' => ['rgb' => '64748B']],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
