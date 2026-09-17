<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'jenis',
            'model',
            'type_spec',
            'serial_number',
            'hostname',
            'buy_date',
            'vendor',
            'unit_loc',
            'dept',
            'pengguna',
            'position_user',
            'note',
            'no_aset_local',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Laptop',
                'Lenovo ThinkPad T14 Gen 3',
                'Core i7-1260P, 16GB RAM, 512GB NVMe SSD',
                'PF3X9021',
                'LAP-IT-01',
                '2024-01-15',
                'PT Mitra Solusi',
                'Head Office Lt 3',
                'Information Technology',
                'Budi Santoso',
                'Senior Developer',
                'Kondisi mulus, kelengkapan charger & tas',
                '', // Kosongkan agar otomatis di-generate sistem
            ],
            [
                'Monitor',
                'Dell 24 Inch P2419H',
                'Full HD 1080p, IPS Panel, Height Adjustable',
                'CN09X201',
                '',
                '2024-02-10',
                'PT Sentra Graha',
                'Head Office Lt 2',
                'Finance',
                'Gudang',
                '',
                'Unit cadangan di gudang IT',
                '',
            ],
            [
                'PC Desktop',
                'HP ProDesk 400 G7 SFF',
                'Core i5-10500, 8GB RAM, 256GB SSD',
                'HP99281A',
                'PC-ACC-02',
                '2023-11-20',
                'PT Kharisma Digital',
                'Head Office Lt 1',
                'Accounting',
                'Siti Rahma',
                'Staff Accounting',
                'Diserahkan lengkap dengan monitor & keyboard',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3874FF'], // Phoenix primary color
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        return [];
    }
}
