<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PersonInChargeController;
use App\Http\Controllers\AssetFunctionController;
use App\Http\Controllers\FundingSourceController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\PublicAssetController;
use Illuminate\Support\Facades\Route;

// Rute untuk halaman publik
Route::get('/aset/{asset_code_ypt}', [PublicAssetController::class, 'show'])->name('public.assets.show');

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

    // Route untuk CRUD Gedung
    Route::resource('buildings', BuildingController::class)->except(['show', 'create', 'edit']);

    // Route untuk CRUD Ruangan
    Route::resource('rooms', RoomController::class)->except(['show', 'create', 'edit']);
    // Rute baru untuk menangani impor Excel
    Route::post('/rooms/import', [RoomController::class, 'import'])->name('rooms.import');
    // Route untuk CRUD Kategori
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
    // Route untuk CRUD Fakultas
    Route::resource('faculties', FacultyController::class)->except(['show', 'create', 'edit']);
    Route::post('/faculties/import', [FacultyController::class, 'import'])->name('faculties.import');
    // Route untuk CRUD Prodi/Unit
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::post('/departments/import', [DepartmentController::class, 'import'])->name('departments.import');
    // Route untuk CRUD Penanggung Jawab
    Route::resource('persons-in-charge', PersonInChargeController::class)
        ->parameters(['persons-in-charge' => 'personInCharge'])
        ->except(['show', 'create', 'edit']);
    Route::post('/persons-in-charge/import', [PersonInChargeController::class, 'import'])->name('persons-in-charge.import');
    // Route untuk CRUD Fungsi Barang
    Route::resource('asset-functions', AssetFunctionController::class)
        ->parameters(['asset-functions' => 'assetFunction'])
        ->except(['show', 'create', 'edit']);
    Route::post('/asset-functions/import', [AssetFunctionController::class, 'import'])->name('asset-functions.import');
    // Route untuk CRUD Jenis Pendanaan
    Route::resource('funding-sources', FundingSourceController::class)
        ->parameters(['funding-sources' => 'fundingSource'])
        ->except(['show', 'create', 'edit']);
    Route::post('/funding-sources/import', [FundingSourceController::class, 'import'])->name('funding-sources.import');
    // Route untuk CRUD Lembaga
    Route::resource('institutions', InstitutionController::class)->except(['show', 'create', 'edit']);
    Route::post('/institutions/import', [InstitutionController::class, 'import'])->name('institutions.import');
    // Route untuk CRUD Aset
    Route::resource('assets', AssetController::class);
    // Route untuk menangani halaman cetak label
    Route::get('/assets-print-labels', [AssetController::class, 'printLabels'])->name('assets.printLabels');
    // Route baru untuk Batch Entry
    Route::get('/assets/batch/create', [AssetController::class, 'batchCreate'])->name('assets.batchCreate');
    Route::post('/assets/batch/store', [AssetController::class, 'batchStore'])->name('assets.batchStore');
    // Route baru untuk Impor Batch Aset
    Route::post('/assets/import-batch', [AssetController::class, 'importBatch'])->name('assets.importBatch');
});

require __DIR__ . '/auth.php';
