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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Vecino ----------
Route::middleware(['auth', 'role:vecino'])->group(function () {
    Route::get('/mi-panel', function () {
        return 'Acceso permitido: panel del vecino';
    })->name('vecino.panel');

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

// ---------- Finanzas (módulo pendiente: Sprint 2) ----------
Route::middleware(['auth', 'role:presidente,tesorero'])->group(function () {
    Route::get('/finanzas', function () {
        return 'Acceso permitido: módulo financiero (solo Admin, Presidente o Tesorero)';
    })->name('finanzas.index');
});

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
    });

    // Agenda de firmas
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [CitaController::class, 'agenda'])->name('index');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::patch('/{cita}/liberar', [CitaController::class, 'liberar'])->name('liberar');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('destroy');
    });

    // Certificados
    Route::prefix('admin/certificados')->name('admin.certificados.')->group(function () {
        Route::get('/', [CertificadoAdminController::class, 'index'])->name('index');
        Route::get('/crear', [CertificadoAdminController::class, 'create'])->name('create');
        Route::post('/', [CertificadoAdminController::class, 'store'])->name('store');
        Route::get('/{certificado}', [CertificadoAdminController::class, 'show'])->whereNumber('certificado')->name('show');
        Route::post('/{certificado}/emitir', [CertificadoAdminController::class, 'emitir'])->whereNumber('certificado')->name('emitir');
        Route::post('/{certificado}/rechazar', [CertificadoAdminController::class, 'rechazar'])->whereNumber('certificado')->name('rechazar');
        Route::post('/{certificado}/anular', [CertificadoAdminController::class, 'anular'])->whereNumber('certificado')->name('anular');
        Route::get('/{certificado}/pdf', [CertificadoAdminController::class, 'pdf'])->whereNumber('certificado')->name('pdf');
    });
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
