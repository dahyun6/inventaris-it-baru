<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected ?string $status = null,
        protected ?string $prioritas = null,
        protected ?string $kategori = null,
        protected ?string $year = null,
        protected ?string $month = null
    ) {}

    public function collection()
    {
        $query = Ticket::with(['barang.category', 'user', 'assignedUser'])
            ->latest();

        if (!empty($this->status) && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->prioritas) && $this->prioritas !== 'all') {
            $query->where('prioritas', $this->prioritas);
        }

        if (!empty($this->kategori) && $this->kategori !== 'all') {
            $query->where('kategori', $this->kategori);
        }

        if (!empty($this->year) && $this->year !== 'all') {
            $query->whereYear('created_at', $this->year);
        }

        if (!empty($this->month) && $this->month !== 'all') {
            $query->whereMonth('created_at', $this->month);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO TIKET',
            'TANGGAL PENGAJUAN',
            'PRIORITAS',
            'KATEGORI LAYANAN',
            'JUDUL KELUHAN',
            'NAMA PELAPOR',
            'EMAIL PELAPOR',
            'DEPARTEMEN',
            'LOKASI / MEJA',
            'NO ASET TERKAIT',
            'MODEL PERANGKAT',
            'STATUS TIKET',
            'TEKNISI PIC',
            'SOLUSI PENANGANAN',
            'TANGGAL SELESAI',
        ];
    }

    /**
     * @param Ticket $ticket
     */
    public function map($ticket): array
    {
        return [
            $ticket->no_tiket,
            $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            $ticket->prioritas,
            $ticket->kategori,
            $ticket->judul,
            $ticket->nama_pelapor,
            $ticket->email_pelapor ?? '-',
            $ticket->departemen_pelapor ?? '-',
            $ticket->lokasi_pelapor ?? '-',
            $ticket->barang?->no_aset_local ?? '-',
            $ticket->barang?->nama_barang ?? $ticket->barang?->model ?? '-',
            $ticket->status,
            $ticket->assignedUser?->name ?? 'Belum Ditugaskan',
            $ticket->solusi ?? '-',
            $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->format('d/m/Y H:i') : '-',
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
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
