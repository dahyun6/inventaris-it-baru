<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_pelapor');
            $table->string('email_pelapor')->nullable();
            $table->string('departemen_pelapor')->nullable();
            $table->string('lokasi_pelapor')->nullable();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('kategori')->default('Hardware / Perangkat');
            $table->string('prioritas')->default('Sedang'); // Rendah, Sedang, Tinggi, Kritis
            $table->string('status')->default('Open'); // Open, In Progress, Pending, Resolved, Closed
            $table->string('judul');
            $table->text('deskripsi');
            $table->text('solusi')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_pengirim');
            $table->string('tipe')->default('response'); // response, status_change, note
            $table->text('pesan');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_setelahnya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_responses');
        Schema::dropIfExists('tickets');
    }
};
