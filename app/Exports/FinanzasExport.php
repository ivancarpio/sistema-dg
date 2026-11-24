<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanzasExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected array $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function view(): View
    {
        return view('reportes.finanzas_excel', $this->datos);
    }

    public function title(): string
    {
        return 'Caja y Finanzas';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF4C1D95']],
                'alignment' => ['horizontal' => 'left'],
            ],
        ];
    }
}
