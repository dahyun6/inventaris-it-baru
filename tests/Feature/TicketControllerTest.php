<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
    }

    public function test_user_can_view_ticket_index(): void
    {
        $ticket = Ticket::create([
            'no_tiket'     => 'TCK-2026-0001',
            'user_id'      => $this->staff->id,
            'nama_pelapor' => $this->staff->name,
            'judul'        => 'Koneksi printer terputus',
            'kategori'     => 'Hardware',
            'prioritas'    => 'Sedang',
            'status'       => 'Open',
            'deskripsi'    => 'Printer kantor tidak bisa diakses dari jaringan',
        ]);

        $response = $this->actingAs($this->admin)->get(route('ticket.index'));

        $response->assertOk();
        $response->assertViewIs('ticket.index');
        $response->assertSee('TCK-2026-0001');
    }

    public function test_can_export_tickets_to_excel(): void
    {
        Ticket::create([
            'no_tiket'     => 'TCK-2026-0002',
            'user_id'      => $this->staff->id,
            'nama_pelapor' => $this->staff->name,
            'judul'        => 'Layar monitor berkedip',
            'kategori'     => 'Hardware',
            'prioritas'    => 'Tinggi',
            'status'       => 'In Progress',
            'deskripsi'    => 'Monitor sering mati hidup sendiri',
        ]);

        $response = $this->actingAs($this->admin)->get(route('ticket.export_excel'));

        $response->assertOk();
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', 'rekap_it_helpdesk_')
        );
    }

    public function test_can_view_ticket_report_print(): void
    {
        Ticket::create([
            'no_tiket'     => 'TCK-2026-0003',
            'user_id'      => $this->staff->id,
            'nama_pelapor' => $this->staff->name,
            'judul'        => 'Instalasi driver scanner',
            'kategori'     => 'Software',
            'prioritas'    => 'Rendah',
            'status'       => 'Resolved',
            'deskripsi'    => 'Butuh software pendukung scanner Epson',
        ]);

        $response = $this->actingAs($this->admin)->get(route('ticket.report_print'));

        $response->assertOk();
        $response->assertViewIs('ticket.report-print');
        $response->assertSee('TCK-2026-0003');
    }
}
