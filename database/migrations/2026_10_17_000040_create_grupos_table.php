<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('horarios_uso')->nullable();
            $table->foreignId('representante_id')->nullable()->constrained('socios')->nullOnDelete();
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_correo')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_direccion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};