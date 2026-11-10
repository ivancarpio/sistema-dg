<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CierreMesExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected $items;
    protected $meta;

    public function __construct(array $items, array $meta = [])
    {
        $this->items = $items;
        $this->meta  = $meta;
    }

    public function view(): View
    {
        return view('reportes.cierre_mes_excel', [
            'items'         => $this->items,
            'empresa'       => $this->meta['empresa'] ?? 'DISTRIBUIDORA GUADALUPE',
            'periodo_label' => $this->meta['periodo_label'] ?? ($this->meta['mes'] ?? date('Y-m')),
            'anio'          => $this->meta['anio'] ?? date('Y'),
        ]);
    }

    public function title(): string
    {
        return 'Cierre de Mes';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Fila 1 - Título
            1 => [
                'font' => ['bold' => true, 'size' => 13],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
            // Fila 5 - Cabecera columnas
            5 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
