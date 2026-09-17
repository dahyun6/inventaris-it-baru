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
        Schema::dropIfExists('maintenances');

        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('no_maintenance')->unique()->index();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('jenis_maintenance')->default('Perbaikan / Rusak');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('pelaksana')->default('Internal IT');
            $table->string('nama_teknisi')->nullable();
            $table->string('status')->default('Dalam Proses');
            $table->text('deskripsi_kendala');
            $table->text('tindakan_perbaikan')->nullable();
            $table->string('status_aset_setelahnya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
