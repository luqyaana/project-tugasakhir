<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\ModelArimaController;
use App\Http\Controllers\Admin\SaranPencegahanController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');

Route::get('/tentang-sistem', function () {
    return view('tentang');
})->name('tentang');

//admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', [AuthController::class, 'create'])->name('login');
        Route::get('/login', [AuthController::class, 'create'])->name('login.page');
        Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/keloladata', [DatasetController::class, 'index'])->name('keloladata');
        Route::get('/keloladata/template/download', [DatasetController::class, 'downloadTemplate'])->name('keloladata.template');
        Route::post('/keloladata', [DatasetController::class, 'store'])->name('keloladata.store');
        Route::put('/keloladata/{dataset}/activate', [DatasetController::class, 'activate'])->name('keloladata.activate');
        Route::get('/keloladata/{dataset}', [DatasetController::class, 'show'])->name('keloladata.show');
        Route::delete('/keloladata/{dataset}', [DatasetController::class, 'destroy'])->name('keloladata.destroy');

        Route::get('/model', [ModelArimaController::class, 'index'])->name('model');
        Route::post('/model/run', [ModelArimaController::class, 'run'])->name('model.run');
        Route::put('/model/{modelArima}/publish', [ModelArimaController::class, 'publish'])->name('model.publish');

        Route::get('/kelolasaran', [SaranPencegahanController::class, 'index'])->name('kelolasaran');
        Route::put('/kelolasaran/{saranPencegahan}', [SaranPencegahanController::class, 'update'])->name('kelolasaran.update');
    });
});
