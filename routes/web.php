<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FacultyController;
use Illuminate\Support\Facades\Route;

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
});

require __DIR__ . '/auth.php';
