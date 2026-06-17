<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('categories', function (Blueprint $table) {
        // Menambahkan kolom kode_prefix setelah kolom nama_kategori (sesuaikan nama kolom aslinya jika berbeda)
        $table->string('kode_prefix', 10)->nullable()->after('nama_kategori'); 
    });
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn('kode_prefix');
    });
}
};
