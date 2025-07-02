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
        Schema::create('meja_billiards', function (Blueprint $table) {
            $table->id('meja_id');
            $table->string('nomor_meja', 10);
            $table->string('tipe_meja', 50)->nullable();
            $table->enum('status', ['tersedia', 'digunakan', 'perbaikan'])->default('tersedia');
            $table->decimal('harga_per_jam', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meja_billiards');
    }
};
