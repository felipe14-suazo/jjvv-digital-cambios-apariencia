<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CertificadoAdminController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VerificacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\HorarioFijoController;

// ---------- Público ----------
Route::get('/', function () {
    return view('welcome');
});

// Verificación en línea de certificados (sin cuenta)
Route::get('/verificar', [VerificacionController::class, 'formulario'])->name('verificar.formulario');
Route::post('/verificar', [VerificacionController::class, 'buscar'])->middleware('throttle:20,1')->name('verificar.buscar');
Route::get('/verificar/{codigo}', [VerificacionController::class, 'mostrar'])->middleware('throttle:30,1')->name('verificar.mostrar');

// ---------- Cualquier usuario autenticado ----------
Route::get('/dashboard', function () {
    $usuario = auth()->user();
    $resumen = [];

    if ($usuario->hasRole('presidente', 'secretario')) {
        $resumen[] = [
            'etiqueta' => 'Solicitudes de socio pendientes',
            'valor' => \App\Models\Socio::where('estado', 'pendiente')->count(),
            'ruta' => route('socios.index'),
        ];
        $resumen[] = [
            'etiqueta' => 'Socios pendientes de firma',
            'valor' => \App\Models\Socio::where('estado', 'por_firmar')->count(),
            'ruta' => route('agenda.index'),
        ];
        $resumen[] = [
            'etiqueta' => 'Certificados por revisar',
            'valor' => \App\Models\Certificado::where('estado', 'solicitado')->count(),
            'ruta' => route('admin.certificados.index'),
        ];
    }

    if ($usuario->hasRole('presidente', 'tesorero')) {
        $resumen[] = [
            'etiqueta' => 'Saldo de caja',
            'valor' => '$'.number_format(\App\Models\Ingreso::saldoCaja(), 0, ',', '.'),
            'ruta' => route('ingresos.index'),
        ];
    }

    return view('dashboard', compact('resumen'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Vecino ----------
Route::middleware(['auth', 'role:vecino'])->group(function () {

    // Solicitud de inscripción como socio
    Route::get('/socios/solicitud', [SocioController::class, 'create'])->name('socios.create');
    Route::post('/socios/solicitud', [SocioController::class, 'store'])->name('socios.store');
    Route::put('/socios/solicitud', [SocioController::class, 'reenviar'])->name('socios.reenviar');

    // Reserva de hora para firmar el libro
    Route::get('/mi-cita', [CitaController::class, 'index'])->name('citas.index');
    Route::post('/mi-cita/{cita}', [CitaController::class, 'reservar'])->name('citas.reservar');
    Route::delete('/mi-cita', [CitaController::class, 'cancelar'])->name('citas.cancelar');

});

// ---------- Socio activo (según la ficha, no según el rol) ----------
    Route::middleware(['auth', 'socio.activo'])->group(function () {
    Route::get('/certificados', [CertificadoController::class, 'index'])->name('certificados.index');
    Route::post('/certificados', [CertificadoController::class, 'store'])->name('certificados.store');
    Route::get('/certificados/{certificado}/pdf', [CertificadoController::class, 'descargar'])
        ->whereNumber('certificado')->name('certificados.descargar');
    });

// ---------- Finanzas: presidente y tesorero ----------
Route::middleware(['auth', 'role:presidente,tesorero'])->group(function () {
    Route::get('/ingresos', [IngresoController::class, 'index'])->name('ingresos.index');
    Route::get('/ingresos/crear', [IngresoController::class, 'create'])->name('ingresos.create');
    Route::post('/ingresos', [IngresoController::class, 'store'])->name('ingresos.store');
    Route::post('/ingresos/{ingreso}/anular', [IngresoController::class, 'anular'])
        ->whereNumber('ingreso')->name('ingresos.anular');
    Route::get('/egresos', [EgresoController::class, 'index'])->name('egresos.index');
    Route::get('/egresos/crear', [EgresoController::class, 'create'])->name('egresos.create');
    Route::post('/egresos', [EgresoController::class, 'store'])->name('egresos.store');
    Route::post('/egresos/{egreso}/anular', [EgresoController::class, 'anular'])
        ->whereNumber('egreso')->name('egresos.anular');
    Route::get('/egresos/{egreso}/comprobante', [EgresoController::class, 'comprobante'])
        ->whereNumber('egreso')->name('egresos.comprobante');
    Route::post('/ingresos/{ingreso}/devolver', [DevolucionController::class, 'store'])
        ->whereNumber('ingreso')->name('ingresos.devolver');
});

// ---------- Calendario: dirigentes y socios activos (el controlador decide qué ve cada uno) ----------
Route::middleware('auth')->get('/calendario', [ReservaController::class, 'index'])->name('calendario.index');

// ---------- Presidente y secretario ----------
Route::middleware(['auth', 'role:presidente,secretario'])->group(function () {

    // Socios
    Route::prefix('socios')->name('socios.')->group(function () {
        Route::get('/', [SocioController::class, 'index'])->name('index');
        Route::get('/crear', [SocioController::class, 'crear'])->name('crear');
        Route::post('/', [SocioController::class, 'guardar'])->name('guardar');
        Route::get('/{socio}', [SocioController::class, 'show'])->whereNumber('socio')->name('show');
        Route::get('/{socio}/editar', [SocioController::class, 'editar'])->whereNumber('socio')->name('editar');
        Route::put('/{socio}', [SocioController::class, 'actualizar'])->whereNumber('socio')->name('actualizar');
        Route::patch('/{socio}', [SocioController::class, 'update'])->whereNumber('socio')->name('update');
        Route::get('/{socio}/documento/{tipo}', [SocioController::class, 'documento'])->whereNumber('socio')->name('documento');
        Route::post('/{socio}/cuenta', [SocioController::class, 'crearCuenta'])
            ->whereNumber('socio')->name('cuenta');
    });

    // Agenda de firmas
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [CitaController::class, 'agenda'])->name('index');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::patch('/{cita}/liberar', [CitaController::class, 'liberar'])->name('liberar');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('destroy');
    });

    // Grupos y organizaciones
    Route::prefix('grupos')->name('grupos.')->group(function () {
        Route::get('/', [GrupoController::class, 'index'])->name('index');
        Route::post('/', [GrupoController::class, 'store'])->name('store');
        Route::get('/{grupo}', [GrupoController::class, 'show'])->whereNumber('grupo')->name('show');
        Route::put('/{grupo}', [GrupoController::class, 'update'])->whereNumber('grupo')->name('update');
        Route::post('/{grupo}/representante', [GrupoController::class, 'asignarRepresentante'])
            ->whereNumber('grupo')->name('representante');
        Route::put('/{grupo}/contacto', [GrupoController::class, 'actualizarContacto'])
            ->whereNumber('grupo')->name('contacto');
        Route::post('/{grupo}/horarios', [HorarioFijoController::class, 'store'])
            ->whereNumber('grupo')->name('horarios.store');
        Route::post('/{grupo}/horarios/generar', [HorarioFijoController::class, 'generar'])
            ->whereNumber('grupo')->name('horarios.generar');
        Route::put('/horarios/{horario}', [HorarioFijoController::class, 'update'])
            ->whereNumber('horario')->name('horarios.update');
        Route::delete('/horarios/{horario}', [HorarioFijoController::class, 'destroy'])
            ->whereNumber('horario')->name('horarios.destroy');
    });

    // Certificados
    Route::prefix('admin/certificados')->name('admin.certificados.')->group(function () {
        Route::get('/', [CertificadoAdminController::class, 'index'])->name('index');
        Route::get('/crear', [CertificadoAdminController::class, 'create'])->name('create');
        Route::post('/', [CertificadoAdminController::class, 'store'])->name('store');
        Route::get('/{certificado}/comprobante', [CertificadoAdminController::class, 'comprobante'])
            ->whereNumber('certificado')->name('comprobante');
        Route::get('/{certificado}', [CertificadoAdminController::class, 'show'])->whereNumber('certificado')->name('show');
        Route::post('/{certificado}/emitir', [CertificadoAdminController::class, 'emitir'])->whereNumber('certificado')->name('emitir');
        Route::post('/{certificado}/rechazar', [CertificadoAdminController::class, 'rechazar'])->whereNumber('certificado')->name('rechazar');
        Route::post('/{certificado}/anular', [CertificadoAdminController::class, 'anular'])->whereNumber('certificado')->name('anular');
        Route::get('/{certificado}/pdf', [CertificadoAdminController::class, 'pdf'])->whereNumber('certificado')->name('pdf');
        Route::post('/{certificado}/retirar', [CertificadoAdminController::class, 'retirar'])
            ->whereNumber('certificado')->name('retirar');
    });

    // Parámetros del sistema
    Route::get('/parametros', [ParametroController::class, 'index'])->name('parametros.index');
    Route::put('/parametros', [ParametroController::class, 'update'])->name('parametros.update');

    // Calendario: ingreso de reservas
    Route::post('/calendario/reservas', [ReservaController::class, 'store'])->name('reservas.store');
});

// ---------- Auditoría: solo presidente ----------
Route::middleware(['auth', 'role:presidente'])->get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

// ---------- Administrador: usuarios y roles ----------
Route::middleware(['auth', 'role:admin'])
    ->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/crear', [UsuarioController::class, 'create'])->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::patch('/{usuario}', [UsuarioController::class, 'update'])->name('update');
    });

require __DIR__.'/auth.php';
