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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookAssetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\AssetAssignmentHistoryController;
use App\Http\Controllers\AssignedAssetController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\MaintenanceHistoryController;
use App\Http\Controllers\AssetInspectionController;
use App\Http\Controllers\InspectionHistoryController;
use App\Http\Controllers\VehicleLogController;
use App\Http\Controllers\AssetDisposalController;
use Illuminate\Support\Facades\Route;

// Rute untuk halaman publik
Route::get('/aset/{asset_code_ypt}', [PublicAssetController::class, 'show'])->name('public.assets.show');

// Rute Verifikasi Dokumen Publik
Route::get('/verify/document/{docNumber}', [PublicVerificationController::class, 'verify'])
    ->where('docNumber', '.*')
    ->name('public.verify');

// Rute Verifikasi Dokumen Publik Maintenance
Route::get('/verify/maintenance/{docNumber}', [PublicVerificationController::class, 'verifyMaintenance'])
    ->where('docNumber', '.*')->name('public.verifyMaintenance');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

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
    // Book Asset Management Routes
    Route::get('/books', [BookAssetController::class, 'index'])->name('books.index');
    Route::get('/books/export-excel', [BookAssetController::class, 'exportExcel'])->name('books.exportExcel');
    Route::get('/books/download-pdf', [BookAssetController::class, 'downloadPDF'])->name('books.downloadPDF');
    // Route untuk CRUD Pegawai
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    // Route untuk proses serah terima aset
    Route::post('/assets/{asset}/assign', [AssetAssignmentController::class, 'store'])->name('assets.assign');
    Route::post('/assets/assignment/{assignment}/return', [AssetAssignmentController::class, 'returnAsset'])->name('assets.return');
    Route::get('/assets/assignment/{assignment}/download-bast/{type}', [AssetAssignmentController::class, 'downloadBast'])
        ->whereIn('type', ['checkout', 'return']) // Hanya izinkan 'checkout' atau 'return'
        ->name('assignments.downloadBast');
    // Route untuk Riwayat Inventaris
    Route::get('/inventory-history', [AssetAssignmentHistoryController::class, 'index'])->name('inventory.history');
    Route::get('/inventory-history/export-excel', [AssetAssignmentHistoryController::class, 'exportExcel'])->name('inventory.exportExcel');
    Route::get('/inventory-history/download-pdf', [AssetAssignmentHistoryController::class, 'downloadPDF'])->name('inventory.downloadPDF');
    // Route untuk Halaman Aset Terpasang/Inventaris Pegawai
    Route::get('/assigned-assets', [AssignedAssetController::class, 'index'])->name('assignedAssets.index');
    // Route untuk Maintenance Aset
    Route::post('/assets/{asset}/maintenance', [AssetMaintenanceController::class, 'store'])->name('maintenance.store');
    Route::delete('/maintenance/{maintenance}', [AssetMaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/maintenance/{maintenance}/download-report', [AssetMaintenanceController::class, 'downloadReport'])->name('maintenance.downloadReport');
    // Route untuk Riwayat Maintenance
    Route::get('/maintenance-history', [MaintenanceHistoryController::class, 'index'])->name('maintenance.history');
    Route::get('/maintenance-history/export-excel', [MaintenanceHistoryController::class, 'exportExcel'])->name('maintenance.exportExcel');
    Route::get('/maintenance-history/download-pdf', [MaintenanceHistoryController::class, 'downloadPDF'])->name('maintenance.downloadPDF');
    // Route untuk Inspeksi Aset
    Route::post('/assets/{asset}/inspection', [AssetInspectionController::class, 'store'])->name('inspections.store');
    // Route::delete('/inspection/{inspection}', [AssetInspectionController::class, 'destroy'])->name('inspections.destroy'); // Aktifkan jika perlu hapus
    Route::get('/inspection/{inspection}/download-bast', [AssetInspectionController::class, 'downloadBast'])->name('inspections.downloadBast');
    // Route untuk Riwayat Inspeksi
    Route::get('/inspection-history', [InspectionHistoryController::class, 'index'])->name('inspection.history');
    Route::get('/inspection-history/export-excel', [InspectionHistoryController::class, 'exportExcel'])->name('inspection.exportExcel');
    Route::get('/inspection-history/download-pdf', [InspectionHistoryController::class, 'downloadPDF'])->name('inspection.downloadPDF');
    // Route untuk Log Kendaraan <-- TAMBAHKAN BLOK INI
    Route::get('/vehicle-logs', [VehicleLogController::class, 'index'])->name('vehicleLogs.index');
    Route::post('/vehicles/{asset}/checkout', [VehicleLogController::class, 'storeCheckout'])->name('vehicles.checkout');
    Route::post('/vehicle-logs/{log}/checkin', [VehicleLogController::class, 'storeCheckin'])->name('vehicleLogs.checkin');
    Route::get('/vehicle-logs/{log}/download-bast/{type}', [VehicleLogController::class, 'downloadBast'])
        ->whereIn('type', ['checkout', 'checkin'])
        ->name('vehicleLogs.downloadBast');
    Route::get('/vehicle-logs/export-excel', [VehicleLogController::class, 'exportExcel'])->name('vehicleLogs.exportExcel');
    Route::get('/vehicle-logs/download-pdf', [VehicleLogController::class, 'downloadPDF'])->name('vehicleLogs.downloadPDF');
    // Route untuk Disposal Aset <-- TAMBAHKAN BLOK INI
    Route::get('/assets/{asset}/dispose', [AssetDisposalController::class, 'create'])->name('disposals.create');
    Route::post('/assets/{asset}/dispose', [AssetDisposalController::class, 'store'])->name('disposals.store');
    Route::get('/disposal/{asset}/download-baph', [AssetDisposalController::class, 'downloadBaph'])->name('disposals.downloadBaph'); // Nanti pakai ID asset
});

require __DIR__ . '/auth.php';
