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
        if (!Schema::hasTable('lokasi_units')) {
            Schema::create('lokasi_units', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lokasi')->unique();
                $table->string('kode_lokasi')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('lokasi_units', function (Blueprint $table) {
                if (!Schema::hasColumn('lokasi_units', 'nama_lokasi')) {
                    $table->string('nama_lokasi')->unique()->after('id');
                }
                if (!Schema::hasColumn('lokasi_units', 'kode_lokasi')) {
                    $table->string('kode_lokasi')->nullable()->after('nama_lokasi');
                }
                if (!Schema::hasColumn('lokasi_units', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('kode_lokasi');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_units');
    }
};
