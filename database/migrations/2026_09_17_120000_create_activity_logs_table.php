<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name');
            $table->string('module', 50)->comment('Aset, Kategori, Departemen, Lokasi, Vendor, Maintenance, Helpdesk, User, Handover');
            $table->string('action', 50)->comment('CREATE, UPDATE, DELETE, HANDOVER, COMPLETE, STATUS_CHANGE');
            $table->string('subject_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['module', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
