<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->string('medio_pago', 20)->nullable();   // efectivo | transferencia
            $table->string('talonario', 30)->nullable();    // si es efectivo
            $table->date('fecha_retiro')->nullable();       // RFS-32
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn(['medio_pago', 'talonario', 'fecha_retiro']);
        });
    }
};