<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mempercepat dua query dashboard: pencarian wilayah terakhir per IP
 * (dipakai saat mencatat kunjungan) dan hitungan pengunjung unik.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_logs', function (Blueprint $table) {
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('visit_logs', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
        });
    }
};
