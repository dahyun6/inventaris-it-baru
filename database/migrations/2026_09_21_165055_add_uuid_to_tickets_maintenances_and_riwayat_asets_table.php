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
        if (!Schema::hasColumn('tickets', 'uuid')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
            foreach (\Illuminate\Support\Facades\DB::table('tickets')->get() as $row) {
                \Illuminate\Support\Facades\DB::table('tickets')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
            }
        }

        if (!Schema::hasColumn('maintenances', 'uuid')) {
            Schema::table('maintenances', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
            foreach (\Illuminate\Support\Facades\DB::table('maintenances')->get() as $row) {
                \Illuminate\Support\Facades\DB::table('maintenances')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
            }
        }

        if (!Schema::hasColumn('riwayat_asets', 'uuid')) {
            Schema::table('riwayat_asets', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
            foreach (\Illuminate\Support\Facades\DB::table('riwayat_asets')->get() as $row) {
                \Illuminate\Support\Facades\DB::table('riwayat_asets')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'uuid')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }

        if (Schema::hasColumn('maintenances', 'uuid')) {
            Schema::table('maintenances', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }

        if (Schema::hasColumn('riwayat_asets', 'uuid')) {
            Schema::table('riwayat_asets', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
