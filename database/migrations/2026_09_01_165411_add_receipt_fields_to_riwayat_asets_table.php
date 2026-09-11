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
        Schema::table('riwayat_asets', function (Blueprint $table) {
            $table->string('no_surat')->nullable()->index()->after('id');
            $table->string('diserahkan_oleh')->nullable()->after('user_id');
            $table->string('penerima_nama')->nullable()->after('diserahkan_oleh');
            $table->string('penerima_dept')->nullable()->after('penerima_nama');
            $table->string('penerima_jabatan')->nullable()->after('penerima_dept');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_asets', function (Blueprint $table) {
            $table->dropColumn([
                'no_surat',
                'diserahkan_oleh',
                'penerima_nama',
                'penerima_dept',
                'penerima_jabatan'
            ]);
        });
    }
};
