<?php

namespace App\Http\Controllers;

// Impor model yang kita butuhkan
use App\Models\MaintenanceSchedule;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request; // Untuk menangani data form

class MaintenanceScheduleController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal pemeliharaan.
     */
    public function index()
    {
        // Ambil semua jadwal, urutkan dari yang terbaru
        // 'with' (Eager Loading) penting agar tidak terjadi N+1 query
        $schedules = MaintenanceSchedule::with(['asset', 'assignedTo'])
            ->latest()
            ->paginate(15); // Paginasi 15 data per halaman

        // Kirim data ke view
        return view('maintenance_schedules.index', compact('schedules'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru.
     */
    public function create()
    {
        // Kita butuh daftar Aset dan User (Teknisi) untuk dropdown di form
        $assets = Asset::orderBy('name')->get();

        // Asumsi Anda bisa memfilter user mana yang 'teknisi'
        // Jika tidak, ganti saja dengan User::all()
        $users = User::orderBy('name')->get();

        return view('maintenance_schedules.create', compact('assets', 'users'));
    }

    /**
     * Menyimpan data dari form 'create' ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'maintenance_type' => 'required|string',
            'schedule_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // 2. Set status default (meski sudah ada di DB, ini lebih eksplisit)
        $validatedData['status'] = 'scheduled';

        // 3. Simpan data ke database
        // Ini bisa dilakukan karena kita sudah atur $fillable di Model
        MaintenanceSchedule::create($validatedData);

        // 4. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('maintenance-schedules.index')
            ->with('success', 'Jadwal pemeliharaan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu jadwal pemeliharaan.
     */
    public function show(MaintenanceSchedule $maintenanceSchedule)
    {
        // Laravel's Route Model Binding otomatis akan fetch data
        // $maintenanceSchedule sudah berisi data berdasarkan ID dari URL

        // Kita bisa load relasinya jika perlu
        $maintenanceSchedule->load(['asset', 'assignedTo']);

        return view('maintenance_schedules.show', compact('maintenanceSchedule'));
    }

    /**
     * Menampilkan form untuk mengedit jadwal.
     */
    public function edit(MaintenanceSchedule $maintenanceSchedule)
    {
        // Sama seperti create(), kita butuh data Aset dan User untuk dropdown
        $assets = Asset::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('maintenance_schedules.edit', compact('maintenanceSchedule', 'assets', 'users'));
    }

    /**
     * Memperbarui data di database dari form 'edit'.
     */
    public function update(Request $request, MaintenanceSchedule $maintenanceSchedule)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'maintenance_type' => 'required|string',
            'schedule_date' => 'required|date',
            'description' => 'nullable|string',

            // Ini untuk saat teknisi meng-update pekerjaan
            'status' => 'nullable|string|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        // 2. LOGIKA PENTING: Jika status diubah jadi 'completed',
        //    catat tanggal selesainya.
        if (isset($validatedData['status']) && $validatedData['status'] == 'completed') {
            $validatedData['completed_at'] = now(); // 'now()' adalah helper Laravel untuk waktu saat ini
        }

        // 3. Update data
        $maintenanceSchedule->update($validatedData);

        // 4. Redirect
        return redirect()->route('maintenance-schedules.index')
            ->with('success', 'Jadwal pemeliharaan berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal pemeliharaan dari database.
     */
    public function destroy(MaintenanceSchedule $maintenanceSchedule)
    {
        // Hapus data
        $maintenanceSchedule->delete();

        // Redirect kembali ke index dengan pesan sukses
        return redirect()->route('maintenance-schedules.index')
            ->with('success', 'Jadwal pemeliharaan berhasil dihapus.');
    }

    /**
     * Menampilkan form untuk membuat jadwal massal.
     */
    public function createBulk()
    {
        // Ambil semua aset untuk ditampilkan di tabel checklist
        // Eager load relasi agar bisa ditampilkan di tabel (misal: kategori & lokasi)
        $assets = Asset::with(['category', 'room', 'building'])->orderBy('name')->get();

        // Ambil semua user (teknisi) untuk dropdown
        $users = User::orderBy('name')->get();

        return view('maintenance_schedules.create-bulk', compact('assets', 'users'));
    }

    /**
     * Menyimpan data jadwal massal dari form 'createBulk'.
     */
    public function storeBulk(Request $request)
    {
        // 1. Validasi data
        $validatedData = $request->validate([
            // Validasi untuk detail pekerjaan
            'title' => 'required|string|max:255',
            'maintenance_type' => 'required|string',
            'schedule_date' => 'required|date',
            'description' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',

            // Validasi untuk aset yang dipilih (HARUS BERUPA ARRAY)
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|exists:assets,id', // Cek tiap ID di array
        ]);

        $assetIds = $validatedData['asset_ids'];
        $count = 0;

        // 2. Siapkan data yang akan di-insert
        // Kita gunakan query builder `insert()` agar jauh lebih cepat
        // daripada looping dan create() satu per satu.

        $recordsToInsert = [];
        $now = now(); // Waktu saat ini untuk timestamp

        foreach ($assetIds as $assetId) {
            $recordsToInsert[] = [
                'asset_id' => $assetId,
                'assigned_to_user_id' => $validatedData['assigned_to_user_id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'maintenance_type' => $validatedData['maintenance_type'],
                'schedule_date' => $validatedData['schedule_date'],
                'status' => 'scheduled', // Status default
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;
        }

        // 3. Masukkan semua data ke DB dalam satu query
        if (!empty($recordsToInsert)) {
            MaintenanceSchedule::insert($recordsToInsert);
        }

        // 4. Redirect kembali ke halaman index
        return redirect()->route('maintenance-schedules.index')
            ->with('success', "Penjadwalan massal berhasil dibuat untuk $count aset.");
    }
}
