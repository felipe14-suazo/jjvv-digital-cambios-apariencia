<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->string('tipo_cuota', 20)->nullable();          // ordinaria | extraordinaria (RFS-60)
            $table->timestamp('anulado_en')->nullable();           // RFS-67: se anula, no se borra
            $table->text('motivo_anulacion')->nullable();
            // Sin clave foránea a propósito: el autor de la anulación debe conservarse (ver bloque 0 pendiente).
            $table->unsignedBigInteger('anulado_por')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropColumn(['tipo_cuota', 'anulado_en', 'motivo_anulacion', 'anulado_por']);
        });
    }
};