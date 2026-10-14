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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->integer('year');
            $table->string('pdf_path');
            $table->string('region')->nullable();
            $table->integer('page_count')->nullable();
            $table->string('file_size')->nullable();
            $table->text('extracted_text')->nullable();
            $table->text('summary')->nullable();
            $table->json('topics')->nullable();
            $table->json('keywords')->nullable();
            $table->json('key_points')->nullable();
            $table->json('indicators')->nullable();
            $table->json('trends')->nullable();
            $table->json('page_locations')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('status')->default('Menunggu Proses');
            $table->string('uploaded_by')->default('Admin BPS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
