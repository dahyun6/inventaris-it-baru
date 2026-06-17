<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Relasi ke tabel kategori (Kolom 'jenis' di Excel)
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            // Kolom dari Excel
            $table->string('no_aset_local')->nullable()->unique();
            $table->string('model'); 
            $table->text('type_spec')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('hostname')->nullable();
            $table->date('buy_date')->nullable();
            $table->string('vendor')->nullable();
            $table->string('unit_loc')->nullable();
            $table->string('dept')->nullable();
            $table->string('pengguna')->nullable();
            $table->string('position_user')->nullable();
            $table->text('note')->nullable();
            
            // Kolom Sistem
            $table->string('status')->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};