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
            'items'          => $this->items,
            'empresa'        => $this->meta['empresa'] ?? 'KRONA DISTRIBUIDORA GUADALUPE',
            'anio'           => $this->meta['anio'] ?? date('Y'),
            'gerente'        => $this->meta['gerente'] ?? 'LUIS ANTENOR CALDERON RODRIGUEZ',
            'encargado'      => $this->meta['encargado'] ?? 'AJAEL',
            'encargada_inv'  => $this->meta['encargada_inv'] ?? 'CIELO MADELEN CALDERON POMA',
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
