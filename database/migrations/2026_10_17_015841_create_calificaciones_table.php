<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->double('calificacion', 8, 2);
            $table->unsignedBigInteger('materias_x_usuarios_id');
            
            $table->foreign('materias_x_usuarios_id')->references('id')->on('materias_x_usuarios')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};