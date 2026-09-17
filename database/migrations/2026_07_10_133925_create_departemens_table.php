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
        if (!Schema::hasTable('departemens')) {
            Schema::create('departemens', function (Blueprint $table) {
                $table->id();
                $table->string('nama_departemen')->unique();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('departemens', 'nama_departemen')) {
            Schema::table('departemens', function (Blueprint $table) {
                $table->string('nama_departemen')->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
