<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->unsignedSmallInteger('interval_maintenance')->nullable()->after('status')->comment('Interval maintenance rutin dalam bulan (misal: 3, 6, 12)');
            $table->date('tgl_maintenance_berikutnya')->nullable()->after('interval_maintenance')->comment('Estimasi tanggal jadwal maintenance berikutnya');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['interval_maintenance', 'tgl_maintenance_berikutnya']);
        });
    }
};
