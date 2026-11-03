<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            // Servicios sin local (peluquería, veterinario o paseador que va a casa): no salen en el mapa
            $table->boolean('a_domicilio')->default(false)->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            $table->dropColumn('a_domicilio');
        });
    }
};
