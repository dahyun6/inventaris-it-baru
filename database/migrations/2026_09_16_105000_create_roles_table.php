<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });

            DB::table('roles')->insert([
                [
                    'id'           => 1,
                    'name'         => 'super_admin',
                    'display_name' => 'Super Admin',
                    'description'  => 'Akses penuh ke seluruh fitur dan pengaturan sistem',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'id'           => 2,
                    'name'         => 'admin',
                    'display_name' => 'Admin',
                    'description'  => 'Dapat menambah user, mengelola & mengubah aset, serta melihat master data',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'id'           => 3,
                    'name'         => 'staff',
                    'display_name' => 'Staff',
                    'description'  => 'Dapat melihat daftar aset & informasi sesuai departemennya sendiri',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
