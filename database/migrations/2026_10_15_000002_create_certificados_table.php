<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['residencia', 'socio']);
            $table->enum('estado', ['solicitado', 'emitido', 'rechazado', 'anulado'])->default('solicitado');

            // El folio se asigna recién al emitir: así las solicitudes rechazadas no consumen números.
            $table->unsignedSmallInteger('anio')->nullable();
            $table->unsignedInteger('correlativo')->nullable();
            $table->string('folio', 20)->nullable()->unique();
            $table->string('codigo_verificacion', 16)->unique();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();   // cuenta que lo solicitó
            $table->foreignId('socio_id')->nullable()->constrained('socios')->nullOnDelete();

            $table->string('nombre_solicitante');
            $table->string('rut_solicitante', 12);
            $table->string('direccion');
            $table->string('dirigido_a');                       // entidad ante la cual se presenta

            $table->unsignedInteger('aporte')->nullable();      // aporte voluntario en CLP
            $table->boolean('documentos_verificados')->default(false); // RO-02: carnet y boleta vistos en persona
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('otorgado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['anio', 'correlativo']);
            $table->index(['estado', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
