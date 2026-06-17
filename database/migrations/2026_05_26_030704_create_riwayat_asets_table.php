<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_asets', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel barang (jika barang dihapus, riwayatnya ikut terhapus)
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            
            // Relasi ke tabel user (bisa kosong jika dikembalikan ke gudang)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('lokasi');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_serah_terima');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_asets');
    }
};