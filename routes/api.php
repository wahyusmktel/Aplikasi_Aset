<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicAssetApiController;
use App\Http\Controllers\Api\VehicleApiController;
use App\Http\Controllers\Api\AssetAssignmentApiController;
use App\Http\Controllers\Api\BookAssetController;
use App\Http\Controllers\Api\MasterDataController;

// Route untuk Login (Public)
Route::post('/login', [AuthController::class, 'login']);

Route::get('/public/assets/{asset_code_ypt}', [PublicAssetApiController::class, 'show'])
    ->name('api.public.assets.show');

// Route yang butuh Autentikasi (pakai middleware sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // API Kendaraan
    Route::get('/vehicles/available', [VehicleApiController::class, 'availableVehicles']);
    Route::post('/vehicles/checkout', [VehicleApiController::class, 'checkout']);
    Route::post('/vehicle-logs/{log}/checkin', [VehicleApiController::class, 'checkin']);
    Route::get('/vehicle-logs/my-history', [VehicleApiController::class, 'myHistory']);
    Route::get('/vehicle-logs/{log}', [VehicleApiController::class, 'logDetail']); // Detail log

    Route::get('/vehicle-logs/{log}/download-bast/{type}', [VehicleApiController::class, 'downloadBast'])
        ->whereIn('type', ['checkout', 'checkin'])
        ->name('api.vehicleLogs.downloadBast');

    // Detail aset by code (authenticated)
    Route::get('/assets/by-code/{asset_code_ypt}', [AssetAssignmentApiController::class, 'showByCode'])
        ->name('api.assets.showByCode');

    // Peminjaman (checkout/assign) – employee diambil dari user login (bukan dari request)
    Route::post('/assets/{asset}/assign', [AssetAssignmentApiController::class, 'assign'])
        ->name('api.assets.assign');

    // Pengembalian (return)
    Route::post('/assets/assignment/{assignment}/return', [AssetAssignmentApiController::class, 'return'])
        ->name('api.assets.return');

    // Download BAST checkout/return (PDF)
    Route::get('/assets/assignment/{assignment}/download-bast/{type}', [AssetAssignmentApiController::class, 'downloadBast'])
        ->whereIn('type', ['checkout', 'return'])
        ->name('api.assignments.downloadBast');
    Route::get('/assignments/my-history', [AssetAssignmentApiController::class, 'myHistory']) // ADDED
        ->name('api.assignments.myHistory');

    // Api Buku
    Route::get('/books', [BookAssetController::class, 'index'])->name('api.books.index');
    Route::post('/books', [BookAssetController::class, 'store'])->name('api.books.store');
    Route::get('/books/{id}', [BookAssetController::class, 'show'])->name('api.books.show');
    Route::put('/books/{id}', [BookAssetController::class, 'update'])->name('api.books.update');
    Route::patch('/books/{id}', [BookAssetController::class, 'update']);
    Route::delete('/books/{id}', [BookAssetController::class, 'destroy'])->name('api.books.destroy');

    Route::get('/master/institutions', [MasterDataController::class, 'institutions']);
    Route::get('/master/buildings',    [MasterDataController::class, 'buildings']);
    Route::get('/master/rooms',        [MasterDataController::class, 'rooms']);
    Route::get('/master/persons',      [MasterDataController::class, 'persons']);
});
