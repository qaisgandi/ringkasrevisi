<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('publication_id')->nullable()->constrained('publications')->nullOnDelete();
            $table->string('event_type', 20)->default('view'); // view | download
            $table->string('ip_address', 45)->nullable();
            $table->string('region', 100)->nullable(); // provinsi / wilayah hasil geolocation
            $table->string('city', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index('region');
            $table->index('publication_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
