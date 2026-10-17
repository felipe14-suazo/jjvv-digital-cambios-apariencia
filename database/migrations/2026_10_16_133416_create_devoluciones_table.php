<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            // Cuota original (un ingreso). unique: una sola devolución por cuota.
            $table->foreignId('ingreso_id')->unique()->constrained('ingresos');
            $table->date('fecha_devolucion');
            $table->unsignedInteger('monto');                      // positivo; el saldo lo resta (RFS-66)
            $table->text('motivo');                                // obligatorio (RFS-66)
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};