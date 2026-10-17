<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) El enum de SQLite no admite el valor "inactivo": se pasa a texto (la validación queda en la aplicación).
        Schema::table('socios', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->change();
        });

        // 2) Datos de la baja.
        Schema::table('socios', function (Blueprint $table) {
            $table->text('motivo_baja')->nullable();
            $table->date('fecha_baja')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('socios', function (Blueprint $table) {
            $table->dropColumn(['motivo_baja', 'fecha_baja']);
        });
    }
};