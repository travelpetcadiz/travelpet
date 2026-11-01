<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            // Para filtrar. null = no lo sabemos
            $table->boolean('admite_grandes')->nullable()->after('tamano_maximo'); // más de 25 kg
            $table->boolean('sin_suplemento')->nullable()->after('suplemento');
            $table->boolean('puede_entrar')->nullable()->after('zonas_permitidas'); // dentro del local, no solo terraza
            $table->boolean('sin_correa')->nullable()->after('puede_entrar'); // hay zona donde pueden ir sueltos
            $table->boolean('urgencias_24h')->default(false)->index()->after('sin_correa');

            // Fechas en que se puede ir (playas). periodo_tipo: todo | solo | prohibido.
            // desde/hasta en formato DD/MM, o «ramos» para el Domingo de Ramos de cada año
            $table->string('periodo_tipo', 10)->nullable()->after('temporada');
            $table->string('periodo_desde', 5)->nullable()->after('periodo_tipo');
            $table->string('periodo_hasta', 5)->nullable()->after('periodo_desde');
        });
    }

    public function down(): void
    {
        Schema::table('sitios', function (Blueprint $table) {
            $table->dropColumn(['admite_grandes', 'sin_suplemento', 'puede_entrar', 'sin_correa', 'urgencias_24h', 'periodo_tipo', 'periodo_desde', 'periodo_hasta']);
        });
    }
};
