<?php

namespace App\Http\Controllers;

// Impor model yang kita butuhkan
use App\Models\MaintenanceSchedule;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request; // Untuk menangani data form
use Carbon\Carbon;
use App\Exports\MaintenanceSchedulesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceScheduleController extends Controller
{
    // FUNGSI UNTUK MENDAPATKAN DATA TERFILTER (DRY - Don't Repeat Yourself)
    private function getFilteredData(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $query = MaintenanceSchedule::query()->with(['asset', 'assignedTo']);

        $query->when($filters['status'], function ($q, $status) {
            return $q->where('status', $status);
        });
        $query->when($filters['date_from'], function ($q, $date_from) {
            return $q->whereDate('schedule_date', '>=', $date_from);
        });
        $query->when($filters['date_to'], function ($q, $date_to) {
            return $q->whereDate('schedule_date', '<=', $date_to);
        });

        return [
            'query' => $query->latest(),
            'filters' => $filters
        ];
    }

    /**
     * Menampilkan daftar semua jadwal pemeliharaan.
     */
    public function index(Request $request) // Ubah method signature
    {
        // Ambil input filter dari request
        $filters = [
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        // Mulai query
        $query = MaintenanceSchedule::with(['asset', 'assignedTo']);

        // Terapkan filter JIKA ada
        $query->when($filters['status'], function ($q, $status) {
            return $q->where('status', $status);
        });

        $query->when($filters['date_from'], function ($q, $date_from) {
            // Asumsi format 'YYYY-MM-DD'
            return $q->whereDate('schedule_date', '>=', $date_from);
        });

        $query->when($filters['date_to'], function ($q, $date_to) {
            return $q->whereDate('schedule_date', '<=', $date_to);
        });

        // Ambil data (sudah terfilter) dan paginasi
        $schedules = $query->latest()->paginate(15)
            ->withQueryString(); // withQueryString agar filter tetap ada di link paginasi

        // Kirim data ke view
        // Kita kirim 'filters' agar form bisa menampilkan nilai filter saat ini
        return view('maintenance_schedules.index', compact('schedules', 'filters'));
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

    /**
     * Menampilkan form untuk update status massal.
     * (Menggunakan DataTables, jadi kita GET semua data yang relevan)
     */
    public function bulkEdit()
    {
        // Ambil SEMUA jadwal yang butuh tindakan
        // Kita tidak mau menampilkan yang sudah 'completed' atau 'cancelled'
        $schedules = MaintenanceSchedule::with(['asset', 'assignedTo'])
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('schedule_date', 'asc') // Urutkan dari yang paling mendesak
            ->get(); // Ambil semua (DataTables yang akan urus paginasi)

        return view('maintenance_schedules.bulk-edit', compact('schedules'));
    }

    /**
     * Memproses update status massal.
     */
    public function bulkUpdate(Request $request)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'schedule_ids'   => 'required|array|min:1',
            'schedule_ids.*' => 'required|exists:maintenance_schedules,id',
            'status'         => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'notes'          => 'nullable|string',
        ]);

        $count = count($validatedData['schedule_ids']);

        // 2. Siapkan data untuk di-update
        $dataToUpdate = [
            'status' => $validatedData['status'],
        ];

        // 3. Tambahkan catatan JIKA diisi
        // Ini akan menimpa catatan lama.
        if (!empty($validatedData['notes'])) {
            $dataToUpdate['notes'] = $validatedData['notes'];
        }

        // 4. LOGIKA KRUSIAL: Set 'completed_at' jika statusnya 'completed'
        if ($validatedData['status'] == 'completed') {
            $dataToUpdate['completed_at'] = now();
        }

        // 5. Update semua record dalam satu query! (Sangat efisien)
        MaintenanceSchedule::whereIn('id', $validatedData['schedule_ids'])
            ->update($dataToUpdate);

        // 6. Redirect
        return redirect()->route('maintenance-schedules.index')
            ->with('success', "Berhasil mengupdate status $count jadwal pemeliharaan.");
    }

    // METHOD EKSPOR EXCEL
    public function exportExcel(Request $request)
    {
        // Ambil filter dari request
        $filters = $this->getFilteredData($request)['filters'];

        // Buat nama file
        $fileName = 'laporan-pemeliharaan-' . date('Y-m-d') . '.xlsx';

        // Panggil Export Class dan kirimkan filter
        return Excel::download(new MaintenanceSchedulesExport($filters), $fileName);
    }


    // METHOD EKSPOR PDF
    public function exportPdf(Request $request)
    {
        // Ambil query yang sudah terfilter
        $query = $this->getFilteredData($request)['query'];

        // Ambil semua data (jangan paginasi)
        $schedules = $query->get();

        // Buat nama file
        $fileName = 'laporan-pemeliharaan-' . date('Y-m-d') . '.pdf';

        // Load view PDF dengan data
        $pdf = PDF::loadView('maintenance_schedules.pdf', compact('schedules'));

        // Set orientasi kertas (jika perlu)
        $pdf->setPaper('a4', 'landscape'); // 'landscape' (horizontal) atau 'portrait' (vertikal)

        // Download
        return $pdf->download($fileName);
    }
}
