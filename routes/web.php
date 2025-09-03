<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\PortalInquilinoController;
use App\Http\Controllers\ReporteAdminController;
use App\Http\Controllers\InquilinoController;
use App\Http\Controllers\DepartamentoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // CRUD contratos
    Route::resource('contratos', ContratoController::class)->only(['index','create','store','show']);

    // Cuotas
    Route::get('cuotas/{cuota}', [CuotaController::class,'show'])->name('cuotas.show');
    Route::post('cuotas/{cuota}/pagar', [CuotaController::class,'pagar'])->name('cuotas.pagar');

    // Portal inquilino (perfil)
    Route::get('/mi-perfil', [PortalInquilinoController::class,'index'])->name('inquilino.perfil');

    // Admin: ingresos
    Route::get('/admin/ingresos', [ReporteAdminController::class,'ingresos'])->name('admin.ingresos');
});


Route::middleware('auth')->group(function () {
    Route::resource('inquilinos', InquilinoController::class);
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('contratos', ContratoController::class)->only(['index','create','store','show','destroy']);
    Route::get('cuotas', [CuotaController::class,'index'])->name('cuotas.index'); // listado general
    Route::get('cuotas/{cuota}', [CuotaController::class,'show'])->name('cuotas.show');
    Route::post('cuotas/{cuota}/pagar', [CuotaController::class,'pagar'])->name('cuotas.pagar');
});

require __DIR__.'/auth.php';
