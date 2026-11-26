<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TABEL 2 (ANAK) : character_cards
     * Kartu karakter milik user (nama karakter, ras, ki, foto).
     * Relasi: factions (1) ---< (N) character_cards lewat faction_id
     */
    public function up(): void
    {
        Schema::create('character_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faction_id')->constrained('factions')->cascadeOnDelete();
            $table->string('title');                 // nama karakter
            $table->string('race')->nullable();      // ras
            $table->string('ki')->nullable();        // ki (string, karena format API "60.000.000")
            $table->text('notes')->nullable();       // catatan
            $table->string('image_url')->nullable(); // foto
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_cards');
    }
};
