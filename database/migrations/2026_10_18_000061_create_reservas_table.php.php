<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);                          // grupo | arriendo | actividad
            $table->string('estado', 20)->default('confirmada'); // 'pendiente' y 'cancelada' se usan en el 3b
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->string('motivo');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->foreignId('socio_id')->nullable()->constrained('socios')->nullOnDelete();
            $table->foreignId('horario_fijo_id')->nullable()->constrained('horarios_fijos')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('inicio');
            // Generar dos veces el mismo mes no duplica reservas de un horario fijo.
            $table->unique(['horario_fijo_id', 'inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
