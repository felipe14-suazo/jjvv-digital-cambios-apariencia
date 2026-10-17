<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->string('numero_transaccion', 50)->nullable();
            $table->string('comprobante_path')->nullable();
            $table->dateTime('descarga_disponible_hasta')->nullable();
        });

        Schema::create('descargas_certificado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificado_id')->constrained('certificados');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->dateTime('descargado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descargas_certificado');

        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn(['numero_transaccion', 'comprobante_path', 'descarga_disponible_hasta']);
        });
    }
};