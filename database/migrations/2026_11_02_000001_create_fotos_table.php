<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fotos reales que sube el equipo desde el panel para usarlas en la web
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            $table->string('archivo');
            $table->string('descripcion');
            $table->string('lugar')->nullable();
            $table->string('autor')->nullable();
            $table->boolean('para_portada')->default(false)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos');
    }
};
