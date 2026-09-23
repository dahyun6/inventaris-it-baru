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
            $table->string('status_terima')->default('pending')->after('keterangan');
            $table->timestamp('accepted_at')->nullable()->after('status_terima');
            $table->foreignId('accepted_by')->nullable()->after('accepted_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_asets', function (Blueprint $table) {
            $table->dropForeign(['accepted_by']);
            $table->dropColumn(['status_terima', 'accepted_at', 'accepted_by']);
        });
    }
};
