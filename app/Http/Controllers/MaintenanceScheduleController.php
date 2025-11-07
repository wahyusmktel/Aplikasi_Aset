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
        // Ambil semua aset, tapi kali ini DIPAGINASI
        $assets = Asset::with(['category', 'room', 'building'])
            ->orderBy('name')
            ->paginate(50); // Misalnya 50 per halaman

        // Ambil user (teknisi)
        $users = User::orderBy('name')->get();

        // Ambil ID aset yang sudah "disimpan" di session
        $selectedAssetIds = session('bulk_schedule_assets', []);

        return view('maintenance_schedules.create-bulk', compact(
            'assets',
            'users',
            'selectedAssetIds' // Kirim ini ke view
        ));
    }

    /**
     * Menyimpan data jadwal massal dari form 'createBulk'.
     * (Versi BARU yang mengambil ID dari SESSION)
     */
    public function storeBulk(Request $request)
    {
        // 1. Validasi data (PERHATIKAN: 'asset_ids' HILANG dari sini)
        // Kita hanya validasi data pekerjaan, karena ID aset sudah divalidasi
        // oleh method 'toggleBulk' (AJAX) satu per satu.
        $validatedData = $request->validate([
            // Validasi untuk detail pekerjaan
            'title' => 'required|string|max:255',
            'maintenance_type' => 'required|string',
            'schedule_date' => 'required|date',
            'description' => 'nullable|string',
            'assigned_to_user_id' => 'nullable|exists:users,id',

            // Validasi 'asset_ids' tidak lagi ada di sini
        ]);

        // 2. AMBIL ID ASET DARI SESSION, BUKAN DARI REQUEST
        $assetIds = session('bulk_schedule_assets', []);
        $count = 0;

        // 3. VALIDASI BARU: Cek jika session (pilihan aset) kosong
        if (empty($assetIds)) {
            // Jika tidak ada ID di session, kembalikan ke form
            // 'withInput()' penting agar form (title, date, dll) tetap terisi
            return redirect()->back()
                ->withErrors(['asset_ids' => 'Anda belum memilih aset. Silakan centang minimal satu aset.'])
                ->withInput(); // withInput() akan menyimpan input form lama
        }

        // 4. Siapkan data yang akan di-insert (Logika ini SAMA seperti sebelumnya)
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

        // 5. Masukkan semua data ke DB (Logika ini SAMA)
        if (!empty($recordsToInsert)) {
            MaintenanceSchedule::insert($recordsToInsert);
        }

        // 6. PERUBAHAN PENTING: HAPUS SESSION SETELAH BERHASIL DISIMPAN
        // Ini agar "keranjang" pilihan aset kembali kosong untuk penjadwalan berikutnya.
        session()->forget('bulk_schedule_assets');

        // 7. Redirect kembali ke halaman index (Logika ini SAMA)
        return redirect()->route('maintenance-schedules.index')
            ->with('success', "Penjadwalan massal berhasil dibuat untuk $count aset.");
    }

    public function toggleBulk(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:assets,id']);

        $assetId = $request->id;
        $selectedIds = session('bulk_schedule_assets', []);

        if (in_array($assetId, $selectedIds)) {
            // Jika sudah ada, hapus (uncheck)
            $selectedIds = array_diff($selectedIds, [$assetId]);
        } else {
            // Jika belum ada, tambahkan (check)
            $selectedIds[] = $assetId;
        }

        session(['bulk_schedule_assets' => $selectedIds]);

        return response()->json(['count' => count($selectedIds)]);
    }

    public function clearBulk()
    {
        session()->forget('bulk_schedule_assets');
        return redirect()->back()->with('success', 'Pilihan aset berhasil dibersihkan.');
    }
}
