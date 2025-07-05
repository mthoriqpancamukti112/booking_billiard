<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary(); // Kolom kunci (e.g., 'jam_buka')
            $table->string('value');          // Kolom nilai (e.g., '08:00')
            $table->timestamps();
        });

        // Menambahkan nilai default saat tabel dibuat
        DB::table('settings')->insert([
            ['key' => 'jam_buka', 'value' => '08:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jam_tutup', 'value' => '22:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
