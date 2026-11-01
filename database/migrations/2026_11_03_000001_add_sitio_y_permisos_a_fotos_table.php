<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Las fotos pueden ser de un sitio (salen en su ficha) o para el diseño de la web (sitio_id null)
        Schema::table('fotos', function (Blueprint $table) {
            $table->foreignId('sitio_id')->nullable()->after('id')->constrained('sitios')->cascadeOnDelete();
            // De dónde sale y con qué permiso: propia | cedida | wikimedia
            $table->string('origen', 12)->default('propia')->after('autor');
            $table->string('fuente_url')->nullable()->after('origen');
            $table->string('licencia', 60)->nullable()->after('fuente_url');
            $table->string('licencia_url')->nullable()->after('licencia');
            $table->text('permiso')->nullable()->after('licencia_url'); // cómo lo cedió el negocio
            $table->boolean('sin_personas')->default(false)->after('permiso');
            // No sale en la web hasta que alguien del equipo la revisa
            $table->boolean('publicada')->default(false)->index()->after('sin_personas');
            $table->unsignedSmallInteger('orden')->default(0)->after('publicada');
        });
    }

    public function down(): void
    {
        Schema::table('fotos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sitio_id');
            $table->dropColumn(['origen', 'fuente_url', 'licencia', 'licencia_url', 'permiso', 'sin_personas', 'publicada', 'orden']);
        });
    }
};
