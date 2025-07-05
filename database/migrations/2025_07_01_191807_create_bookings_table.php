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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('meja_id')->constrained('meja_billiards', 'meja_id');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->decimal('total_biaya', 10, 2)->nullable();
            $table->enum('status_booking', ['dipesan', 'berlangsung', 'selesai', 'dibatalkan'])->default('dipesan');
            $table->enum('status_pembayaran', ['belum_bayar', 'dp_lunas', 'lunas', 'gagal'])
                ->default('belum_bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
