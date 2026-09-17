<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\StoreTicketResponseRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $prioritas = $request->query('prioritas');
        $kategori = $request->query('kategori');
        $user = Auth::user();

        $tickets = $this->ticketService->getAll($status, $prioritas, $kategori, $user);
        $metrics = $this->ticketService->getMetrics($user);
        $formData = $this->ticketService->getFormData(null, $user);

        return view('ticket.index', array_merge(
            compact('tickets', 'metrics', 'status', 'prioritas', 'kategori'),
            $formData
        ));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($user && $user->isStaff()) {
            $data['user_id'] = $user->id;
            $data['nama_pelapor'] = $user->name;
            $data['email_pelapor'] = $user->email;
            $data['status'] = 'Open';
            $data['assigned_to'] = null;
        }

        $authorName = $user?->name ?? ($data['nama_pelapor'] ?? 'Pelapor');
        $ticket = $this->ticketService->create($data, Auth::id(), $authorName);

        return redirect()->route('ticket.index')
            ->with('success', 'Tiket bantuan IT (' . $ticket->no_tiket . ') berhasil dibuat!');
    }

    public function show(Ticket $ticket): View
    {
        $user = Auth::user();
        if ($user && $user->isStaff() && !$ticket->isOwnedBy($user)) {
            abort(403, 'Anda tidak memiliki akses ke tiket bantuan ini.');
        }

        $ticket->load(['barang.category', 'user', 'assignedUser', 'responses.user']);
        $formData = $this->ticketService->getFormData($ticket->barang_id, $user);

        return view('ticket.show', [
            'ticket'  => $ticket,
            'users'   => $formData['users'],
            'barangs' => $formData['barangs'],
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        if (Auth::user()?->isStaff()) {
            abort(403, 'Staff biasa tidak diizinkan mengubah metadata tiket.');
        }

        $this->ticketService->update($ticket, $request->validated());

        return redirect()->back()
            ->with('success', 'Informasi tiket (' . $ticket->no_tiket . ') berhasil diperbarui!');
    }

    public function response(StoreTicketResponseRequest $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->isStaff() && !$ticket->isOwnedBy($user)) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $payload = $request->validated();
        if ($user && $user->isStaff()) {
            $payload = [
                'pesan' => $payload['pesan'],
                'tipe'  => 'response',
            ];
        }

        $authorName = $user?->name ?? 'User Helpdesk';
        $this->ticketService->addResponse($ticket, $payload, Auth::id(), $authorName);

        return redirect()->route('ticket.show', $ticket->id)
            ->with('success', 'Tanggapan tiket berhasil dikirim!');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        if (Auth::user()?->isStaff()) {
            abort(403, 'Staff biasa tidak diizinkan menghapus tiket.');
        }

        $no = $ticket->no_tiket;
        $this->ticketService->delete($ticket);

        return redirect()->route('ticket.index')
            ->with('success', 'Tiket bantuan (' . $no . ') berhasil dihapus!');
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $status = $request->query('status');
        $prioritas = $request->query('prioritas');
        $kategori = $request->query('kategori');
        $year = $request->query('year');
        $month = $request->query('month');

        return $this->ticketService->exportExcel($status, $prioritas, $kategori, $year, $month);
    }

    public function printReport(Request $request): View
    {
        $status = $request->query('status');
        $prioritas = $request->query('prioritas');
        $kategori = $request->query('kategori');
        $user = Auth::user();

        $tickets = $this->ticketService->getAll($status, $prioritas, $kategori, $user);
        $metrics = $this->ticketService->getMetrics($user);

        return view('ticket.report-print', compact('tickets', 'metrics', 'status', 'prioritas', 'kategori'));
    }

    public function print(Ticket $ticket): View
    {
        $user = Auth::user();
        if ($user && $user->isStaff() && !$ticket->isOwnedBy($user)) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $ticket->load(['barang.category', 'user', 'assignedUser', 'responses.user']);

        return view('ticket.print', compact('ticket'));
    }
}
