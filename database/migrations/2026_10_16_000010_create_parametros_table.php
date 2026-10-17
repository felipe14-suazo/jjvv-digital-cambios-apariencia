<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 60)->unique();
            $table->string('valor', 255);
            $table->string('etiqueta', 120);
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $ahora = now();

        DB::table('parametros')->insert([
            ['clave' => 'sede_hora_inicio', 'valor' => '09:00', 'etiqueta' => 'Hora de apertura de la sede', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'sede_hora_fin', 'valor' => '22:00', 'etiqueta' => 'Hora de cierre de la sede', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'sede_duracion_bloque_min', 'valor' => '60', 'etiqueta' => 'Duración del bloque (minutos)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'certificado_vigencia_dias', 'valor' => '90', 'etiqueta' => 'Vigencia del certificado (días)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'certificado_plazo_descarga_dias', 'valor' => '5', 'etiqueta' => 'Plazo para descargar el certificado (días)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'certificado_residencia_valor', 'valor' => '1000', 'etiqueta' => 'Valor del certificado de residencia (CLP)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'arriendo_plazo_confirmacion_horas', 'valor' => '48', 'etiqueta' => 'Plazo para confirmar un arriendo (horas)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'arriendo_valor', 'valor' => '40000', 'etiqueta' => 'Valor del arriendo (CLP)', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'arriendo_garantia', 'valor' => '40000', 'etiqueta' => 'Garantía del arriendo (CLP)', 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};