<?php

namespace App\Exports;

use App\Models\Arqueo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArqueoExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    protected $arqueo;

    public function __construct(Arqueo $arqueo)
    {
        $this->arqueo = $arqueo;
    }

    public function view(): View
    {
        return view('arqueos.excel', [
            'arqueo' => $this->arqueo,
        ]);
    }

    public function title(): string
    {
        return 'Arqueo #' . $this->arqueo->id;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
            4 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
            8 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF334155']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
