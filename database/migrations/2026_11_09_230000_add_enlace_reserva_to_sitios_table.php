<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            // Enlace de afiliado para reservar (Booking vía Travelpayouts). Solo alojamientos.
            $table->string('enlace_reserva', 1000)->nullable()->after('contacto');
        });
    }

    public function down(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            $table->dropColumn('enlace_reserva');
        });
    }
};
