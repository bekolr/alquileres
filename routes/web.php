<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\PortalInquilinoController;
use App\Http\Controllers\ReporteAdminController;
use App\Http\Controllers\InquilinoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\IndiceIpcController;
use App\Http\Controllers\MovimientoController;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========= RUTA ESPECÍFICA PRIMERO =========
    Route::get('cuotas/vencimientomes', [CuotaController::class, 'filtro'])
        ->name('cuotas.vencimiento');

    // Cuotas (listado general + show + pagar)
    Route::get('cuotas', [CuotaController::class,'index'])->name('cuotas.index');
    Route::get('cuotas/{cuota}', [CuotaController::class,'show'])
        ->whereNumber('cuota')->name('cuotas.show');
    Route::post('cuotas/{cuota}/pagar', [CuotaController::class,'pagar'])
        ->whereNumber('cuota')->name('cuotas.pagar');

    // Portal inquilino
    Route::get('/mi-perfil', [PortalInquilinoController::class,'index'])->name('inquilino.perfil');

    // Admin: ingresos
    Route::get('/admin/ingresos', [ReporteAdminController::class,'ingresos'])->name('admin.ingresos');

    // Recursos (evitá duplicar contratos; ya estaba 2 veces)
    Route::resource('contratos', ContratoController::class)->only(['index','create','store','show','destroy']);
    Route::resource('inquilinos', InquilinoController::class);
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('edificios', EdificioController::class);
    Route::resource('ipc', IndiceIpcController::class);
    Route::resource('users', UserController::class);
    Route::resource('movimientos', MovimientoController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permisos', PermisoController::class);

    // Expensas contrato
    Route::post('contratos/{contrato}/expensas', [ContratoController::class,'actualizarExpensas'])
        ->name('contratos.expensas');
});

require __DIR__.'/auth.php';
