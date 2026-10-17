<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 30);
            $table->date('fecha_pago');
            $table->unsignedInteger('monto');
            $table->enum('medio_pago', ['efectivo', 'transferencia']);
            $table->string('talonario', 30)->nullable();           // efectivo (RFS-59)
            $table->string('numero_transaccion', 50)->nullable();  // transferencia (RFS-59)
            $table->text('observaciones')->nullable();
            // Si el certificado se anula, el ingreso se conserva (RFS-67: no borrar movimientos).
            $table->foreignId('certificado_id')->nullable()->constrained('certificados')->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('concepto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};