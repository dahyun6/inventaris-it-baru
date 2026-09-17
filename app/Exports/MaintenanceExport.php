<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected ?string $status = null,
        protected ?string $year = null,
        protected ?string $month = null
    ) {}

    public function collection()
    {
        $query = Maintenance::with(['barang.category', 'user', 'vendor'])
            ->orderByDesc('tanggal_mulai');

        if (!empty($this->status) && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->year) && $this->year !== 'all') {
            $query->whereYear('tanggal_mulai', $this->year);
        }

        if (!empty($this->month) && $this->month !== 'all') {
            $query->whereMonth('tanggal_mulai', $this->month);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO TIKET',
            'TANGGAL MULAI',
            'TANGGAL SELESAI',
            'NO ASET',
            'NAMA / MODEL PERANGKAT',
            'SERIAL NUMBER (SN)',
            'KATEGORI',
            'JENIS MAINTENANCE',
            'PELAKSANA',
            'MITRA VENDOR',
            'NAMA TEKNISI',
            'BIAYA (RP)',
            'STATUS PENGERJAAN',
            'STATUS ASET SETELAHNYA',
            'DESKRIPSI KENDALA',
            'TINDAKAN PERBAIKAN',
            'ADMIN IT PENCATAT',
        ];
    }

    /**
     * @param Maintenance $maintenance
     */
    public function map($maintenance): array
    {
        return [
            $maintenance->no_maintenance,
            $maintenance->tanggal_mulai ? \Carbon\Carbon::parse($maintenance->tanggal_mulai)->format('d/m/Y') : '-',
            $maintenance->tanggal_selesai ? \Carbon\Carbon::parse($maintenance->tanggal_selesai)->format('d/m/Y') : '-',
            $maintenance->barang?->no_aset_local ?? '-',
            $maintenance->barang?->nama_barang ?? $maintenance->barang?->model ?? '-',
            $maintenance->barang?->serial_number ?? '-',
            $maintenance->barang?->category?->nama_kategori ?? '-',
            $maintenance->jenis_maintenance,
            $maintenance->pelaksana,
            $maintenance->vendor?->nama_vendor ?? '-',
            $maintenance->nama_teknisi ?? '-',
            (float) ($maintenance->biaya ?? 0),
            $maintenance->status,
            $maintenance->status_aset_setelahnya ?? '-',
            $maintenance->deskripsi_kendala,
            $maintenance->tindakan_perbaikan ?? '-',
            $maintenance->user?->name ?? 'Admin IT',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
