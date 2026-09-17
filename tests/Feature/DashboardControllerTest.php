<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        Barang::factory()->count(2)->create(['status' => 'Tersedia']);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('total_aset', 2);
    }

    public function test_ajax_request_returns_trend_json(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('dashboard', [
            'year'  => date('Y'),
            'month' => date('m'),
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'selectedYear',
            'selectedMonth',
            'labels',
            'data_aset_masuk',
            'data_aset_diperbaiki',
            'total_masuk',
            'total_diperbaiki',
        ]);
    }

    public function test_dashboard_renders_with_recent_activity_logs(): void
    {
        \App\Models\ActivityLog::create([
            'user_id'     => $this->user->id,
            'user_name'   => $this->user->name,
            'module'      => 'Aset',
            'action'      => 'CREATE',
            'subject_id'  => 1,
            'subject_name'=> 'AST-001',
            'description' => 'Menambahkan aset baru AST-001',
            'ip_address'  => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('AST-001');
        $response->assertSee('CREATE');
    }
}
