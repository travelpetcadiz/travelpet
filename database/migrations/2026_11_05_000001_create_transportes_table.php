<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Compañías de avión, tren, autobús y barco y sus normas para mascotas (página «Transporte con mascota»)
        Schema::create('transportes', function (Blueprint $table) {
            $table->id();
            $table->string('medio', 20)->index();
            $table->string('nombre');
            $table->string('zona')->nullable();
            $table->boolean('vuela_a_jerez')->default(false);
            $table->boolean('en_cabina')->nullable();
            $table->string('estado', 20)->default('no_consta');
            $table->string('respuesta')->nullable();
            $table->string('peso')->nullable();
            $table->string('transportin')->nullable();
            $table->string('precio')->nullable();
            $table->text('otras_condiciones')->nullable();
            $table->string('enlace', 500)->nullable();
            $table->date('fecha_comprobacion')->nullable();
            $table->unsignedSmallInteger('orden')->default(100);
            $table->boolean('publicado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportes');
    }
};
