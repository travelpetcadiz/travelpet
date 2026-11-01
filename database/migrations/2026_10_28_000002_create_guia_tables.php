<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Geografía: se pueden añadir provincias desde el panel sin tocar código
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('comunidad')->nullable();
            $table->string('pais', 2)->default('ES');
            $table->decimal('latitud', 9, 6)->nullable();
            $table->decimal('longitud', 9, 6)->nullable();
            $table->unsignedTinyInteger('zoom')->default(9);
            $table->boolean('activa')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provincia_id')->constrained('provincias')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('slug');
            $table->string('comarca')->nullable()->index();
            $table->decimal('latitud', 9, 6)->nullable();
            $table->decimal('longitud', 9, 6)->nullable();
            $table->timestamps();

            $table->unique(['provincia_id', 'nombre']);
            $table->unique(['provincia_id', 'slug']);
        });

        Schema::create('sitios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained('municipios')->restrictOnDelete();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('tipo', 40)->index();
            $table->string('subtipo')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('latitud', 9, 6)->nullable();
            $table->decimal('longitud', 9, 6)->nullable();
            $table->text('contacto')->nullable();
            $table->string('admite_perros', 20)->default('no_consta')->index();
            $table->string('tamano_maximo')->nullable();
            $table->string('suplemento')->nullable();
            $table->text('zonas_permitidas')->nullable();
            $table->string('temporada')->nullable();
            $table->text('otras_condiciones')->nullable();
            $table->text('fuente')->nullable();
            $table->date('fecha_comprobacion')->nullable();
            $table->boolean('verificado')->default(false)->index();
            $table->boolean('fuente_oficial')->default(false);
            $table->boolean('publicado')->default(true)->index();
            $table->timestamps();

            $table->unique(['nombre', 'municipio_id']);
        });

        Schema::create('cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sitio_id')->nullable()->constrained('sitios')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sitio_nombre');
            $table->string('municipio')->nullable();
            $table->string('campo');
            $table->text('antes')->nullable();
            $table->text('despues')->nullable();
            $table->string('fuente')->nullable();
            $table->date('fecha');
            $table->timestamps();
        });

        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sitio_id')->constrained('sitios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('estrellas');
            $table->string('pudo_entrar', 10); // si | no
            $table->string('tamano_perro', 20)->nullable();
            $table->text('texto');
            $table->string('estado', 20)->default('pendiente')->index(); // pendiente | aprobada | rechazada
            $table->timestamps();

            $table->unique(['sitio_id', 'user_id']);
        });

        Schema::create('sugerencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sitio_id')->nullable()->constrained('sitios')->nullOnDelete();
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->nullOnDelete();
            $table->string('tipo', 20); // nuevo_sitio | error
            $table->string('nombre')->nullable();
            $table->string('municipio')->nullable();
            $table->text('mensaje');
            $table->string('estado', 20)->default('pendiente')->index(); // pendiente | hecha | descartada
            $table->timestamps();
        });

        Schema::create('viaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sitio_id')->constrained('sitios')->cascadeOnDelete();
            $table->string('estado', 10)->default('quiero'); // quiero | visitado
            $table->timestamps();

            $table->unique(['user_id', 'sitio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viaje');
        Schema::dropIfExists('sugerencias');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('cambios');
        Schema::dropIfExists('sitios');
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('provincias');
    }
};
