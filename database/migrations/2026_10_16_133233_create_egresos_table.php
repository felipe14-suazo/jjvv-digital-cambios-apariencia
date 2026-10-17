<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 30);                        // servicios_basicos | mantencion (RFS-65)
            $table->date('fecha_pago');
            $table->unsignedInteger('monto');
            $table->string('numero_transaccion', 50);
            $table->string('comprobante_path');                    // obligatorio (RFS-65)
            $table->string('pagado_por', 100);                     // quién pagó (RFS-65)
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();

            // Anulación: se anula, no se borra (RFS-67). Misma estructura que ingresos.
            $table->timestamp('anulado_en')->nullable();
            $table->text('motivo_anulacion')->nullable();
            // Sin clave foránea a propósito: el autor de la anulación debe conservarse.
            $table->unsignedBigInteger('anulado_por')->nullable();

            $table->timestamps();

            $table->index('concepto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};