<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Get all tickets with relations and optional filters.
     */
    public function getAll(?string $status = null, ?string $prioritas = null, ?string $kategori = null, ?User $user = null): Collection
    {
        $query = Ticket::with(['barang.category', 'user', 'assignedUser', 'responses'])
            ->orderByRaw("CASE 
                WHEN status = 'Open' THEN 1 
                WHEN status = 'In Progress' THEN 2 
                WHEN status = 'Pending' THEN 3 
                WHEN status = 'Resolved' THEN 4 
                ELSE 5 END")
            ->orderByDesc('created_at');

        if ($user && $user->isStaff()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_pelapor', $user->email)
                  ->orWhere('nama_pelapor', 'like', '%' . $user->name . '%');
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($prioritas && $prioritas !== 'all') {
            $query->where('prioritas', $prioritas);
        }

        if ($kategori && $kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        return $query->get();
    }

    /**
     * Get summary KPI metrics for tickets.
     */
    public function getMetrics(?User $user = null): array
    {
        $query = Ticket::query();

        if ($user && $user->isStaff()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email_pelapor', $user->email)
                  ->orWhere('nama_pelapor', 'like', '%' . $user->name . '%');
            });
        }

        return [
            'total'       => (clone $query)->count(),
            'open'        => (clone $query)->where('status', 'Open')->count(),
            'in_progress' => (clone $query)->where('status', 'In Progress')->count(),
            'resolved'    => (clone $query)->whereIn('status', ['Resolved', 'Closed'])->count(),
            'kritis'      => (clone $query)->where('prioritas', 'Kritis')->whereNotIn('status', ['Resolved', 'Closed'])->count(),
        ];
    }

    /**
     * Generate automatic ticket code (ITH-dmY-0001).
     */
    public function generateNoTiket(): string
    {
        $prefix = 'ITH-' . date('dmY') . '-';
        $countToday = Ticket::where('no_tiket', 'like', $prefix . '%')->count();

        return $prefix . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get form dependencies for create/edit ticket.
     */
    public function getFormData(?int $barangId = null, ?User $user = null): array
    {
        $barangQuery = Barang::with('category');

        if ($user && $user->isStaff()) {
            $barangQuery->where(function ($q) use ($user) {
                $q->where('pengguna', 'like', '%' . $user->name . '%')
                  ->orWhereHas('riwayat', function ($rq) use ($user) {
                      $rq->where('user_id', $user->id)
                        ->orWhere('penerima_nama', 'like', '%' . $user->name . '%');
                  });
            });
        }

        return [
            'autoNo'         => $this->generateNoTiket(),
            'users'          => User::with(['departemen', 'lokasi'])->orderBy('name')->get(),
            'barangs'        => $barangQuery->orderBy('no_aset_local')->get(),
            'selectedBarang' => $barangId ? Barang::with('category')->find($barangId) : null,
        ];
    }

    /**
     * Store new helpdesk ticket.
     */
    public function create(array $data, ?int $authUserId = null, ?string $authorName = null): Ticket
    {
        return DB::transaction(function () use ($data, $authUserId, $authorName) {
            $ticket = Ticket::create($data);

            // Buat response / log pembukaan tiket pertama
            TicketResponse::create([
                'ticket_id'         => $ticket->id,
                'user_id'           => $authUserId,
                'nama_pengirim'     => $authorName ?: ($data['nama_pelapor'] ?? 'System Helpdesk'),
                'tipe'              => 'status_change',
                'pesan'             => 'Tiket bantuan IT resmi dibuat dengan status [Open] dan prioritas [' . $ticket->prioritas . '].',
                'status_sebelumnya' => null,
                'status_setelahnya' => 'Open',
            ]);

            ActivityLogService::log(
                'Helpdesk',
                'CREATE',
                $ticket->no_tiket,
                "Membuat tiket helpdesk {$ticket->no_tiket}: {$ticket->judul} (Pelapor: {$ticket->nama_pelapor})",
                $ticket->id
            );

            return $ticket;
        });
    }

    /**
     * Update existing ticket.
     */
    public function update(Ticket $ticket, array $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $data) {
            $prevStatus = $ticket->status;

            if ($data['status'] === 'Resolved' && $prevStatus !== 'Resolved') {
                $data['resolved_at'] = now();
            } elseif ($data['status'] === 'Closed' && $prevStatus !== 'Closed') {
                $data['closed_at'] = now();
            }

            $ticket->update($data);

            if ($prevStatus !== $data['status']) {
                TicketResponse::create([
                    'ticket_id'         => $ticket->id,
                    'user_id'           => auth()->id(),
                    'nama_pengirim'     => auth()->user()?->name ?? 'Admin IT',
                    'tipe'              => 'status_change',
                    'pesan'             => 'Status tiket diubah dari [' . $prevStatus . '] menjadi [' . $data['status'] . '].',
                    'status_sebelumnya' => $prevStatus,
                    'status_setelahnya' => $data['status'],
                ]);
            }

            ActivityLogService::log(
                'Helpdesk',
                'UPDATE',
                $ticket->no_tiket,
                "Memperbarui data tiket helpdesk {$ticket->no_tiket} (Status: {$ticket->status})",
                $ticket->id
            );

            return $ticket;
        });
    }

    /**
     * Add reply / response / status update to a ticket.
     */
    public function addResponse(Ticket $ticket, array $data, ?int $authUserId, string $authorName): TicketResponse
    {
        return DB::transaction(function () use ($ticket, $data, $authUserId, $authorName) {
            $prevStatus = $ticket->status;
            $newStatus = $data['status'] ?? $prevStatus;

            $updatePayload = [];

            if (!empty($data['assigned_to']) && $data['assigned_to'] != $ticket->assigned_to) {
                $updatePayload['assigned_to'] = $data['assigned_to'];
            }

            if (!empty($data['solusi'])) {
                $updatePayload['solusi'] = $data['solusi'];
            }

            if ($newStatus !== $prevStatus) {
                $updatePayload['status'] = $newStatus;
                if ($newStatus === 'Resolved' && empty($ticket->resolved_at)) {
                    $updatePayload['resolved_at'] = now();
                } elseif ($newStatus === 'Closed' && empty($ticket->closed_at)) {
                    $updatePayload['closed_at'] = now();
                }
            }

            if (!empty($updatePayload)) {
                $ticket->update($updatePayload);
            }

            $response = TicketResponse::create([
                'ticket_id'         => $ticket->id,
                'user_id'           => $authUserId,
                'nama_pengirim'     => $authorName,
                'tipe'              => $data['tipe'] ?? 'response',
                'pesan'             => $data['pesan'],
                'status_sebelumnya' => $prevStatus,
                'status_setelahnya' => $newStatus,
            ]);

            ActivityLogService::log(
                'Helpdesk',
                'RESPONSE',
                $ticket->no_tiket,
                "Menambahkan tanggapan pada tiket {$ticket->no_tiket} (Status: {$newStatus})",
                $ticket->id
            );

            return $response;
        });
    }

    /**
     * Delete ticket.
     */
    public function delete(Ticket $ticket): bool
    {
        $no = $ticket->no_tiket;
        $id = $ticket->id;
        $res = (bool) $ticket->delete();

        if ($res) {
            ActivityLogService::log(
                'Helpdesk',
                'DELETE',
                $no,
                "Menghapus tiket helpdesk {$no}",
                $id
            );
        }

        return $res;
    }

    /**
     * Export tickets report to Excel.
     */
    public function exportExcel(?string $status = null, ?string $prioritas = null, ?string $kategori = null, ?string $year = null, ?string $month = null): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'rekap_it_helpdesk_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TicketExport($status, $prioritas, $kategori, $year, $month),
            $filename
        );
    }
}
