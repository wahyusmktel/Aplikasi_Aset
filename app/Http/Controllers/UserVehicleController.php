<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Employee;
use App\Models\VehicleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class UserVehicleController extends Controller
{
    /**
     * Halaman utama peminjaman kendaraan dinas untuk user.
     */
    public function index()
    {
        $vehicles = Asset::with(['category', 'room'])
            ->where('status', 'aktif')
            ->whereHas('category', fn($q) => $q->where('name', 'KENDARAAN BERMOTOR DINAS / KBM DINAS'))
            ->orderBy('name')
            ->get();

        $employees = Employee::orderBy('name')->get();

        // Riwayat peminjaman kendaraan oleh user ini
        $myLogs = VehicleLog::with(['asset', 'employee', 'driverEmployee'])
            ->where('user_id', Auth::id())
            ->latest('departure_time')
            ->paginate(10);

        return view('user.kendaraan.index', compact('vehicles', 'employees', 'myLogs'));
    }

    /**
     * Simpan data checkout kendaraan dari halaman user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id'              => 'required|exists:assets,id',
            'destination'           => 'required|string|max:255',
            'purpose'               => 'required|string|max:1000',
            'departure_time'        => 'required|date',
            'estimated_return_time' => 'required|date|after:departure_time',
            'driver_type'           => 'required|in:self,school_driver',
            'driver_employee_id'    => 'nullable|exists:employees,id',
            'start_odometer'        => 'required|integer|min:0',
            'fuel_level_start'      => 'required|in:Full,3/4,1/2,1/4,Hampir Habis',
            'start_latitude'        => 'required|numeric',
            'start_longitude'       => 'required|numeric',
            'condition_on_checkout' => 'required|in:Baik,Ada Lecet,Rusak',
        ]);

        $user  = Auth::user();
        $asset = Asset::findOrFail($request->asset_id);

        if ($asset->current_status !== 'Tersedia') {
            return back()->with('error', 'Kendaraan ini sedang tidak tersedia.');
        }

        // Cari employee terkait akun user (opsional, bisa null)
        $employee = Employee::where('user_id', $user->id)->first();

        // Dokumen checkout
        $docNumber = $this->generateDocumentNumber('BASTK');

        $log = VehicleLog::create([
            'asset_id'              => $asset->id,
            'employee_id'           => $employee?->id ?? Employee::orderBy('id')->first()?->id,
            'user_id'               => $user->id,
            'borrower_name'         => $user->name,
            'borrower_nip'          => $user->nip ?? ($employee?->nip ?? '-'),
            'destination'           => $request->destination,
            'purpose'               => $request->purpose,
            'departure_time'        => $request->departure_time,
            'estimated_return_time' => $request->estimated_return_time,
            'driver_type'           => $request->driver_type,
            'driver_employee_id'    => $request->driver_type === 'school_driver' ? $request->driver_employee_id : null,
            'start_odometer'        => $request->start_odometer,
            'start_latitude'        => $request->start_latitude,
            'start_longitude'       => $request->start_longitude,
            'fuel_level_start'      => $request->fuel_level_start,
            'condition_on_checkout' => $request->condition_on_checkout,
            'checkout_doc_number'   => $docNumber,
        ]);

        $asset->update(['current_status' => 'Digunakan']);

        return redirect()->route('user.kendaraan.index')
            ->with('success', "Peminjaman kendaraan \"{$asset->name}\" berhasil dicatat. No. Dokumen: {$docNumber}");
    }

    /**
     * Simpan data checkin (pengembalian) kendaraan dari halaman user.
     */
    public function checkin(Request $request, VehicleLog $log)
    {
        // Pastikan log ini milik user yang sedang login
        if ($log->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'return_time'          => 'required|date',
            'end_odometer'         => 'required|integer|min:' . $log->start_odometer,
            'fuel_level_end'       => 'required|in:Full,3/4,1/2,1/4,Hampir Habis',
            'condition_on_checkin' => 'required|in:Aman,Ada Lecet,Rusak',
            'notes'                => 'nullable|string|max:1000',
            'return_photos.*'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $docNumber = $this->generateDocumentNumber('BAPK');

        // Handle foto upload
        $photoPaths = [];
        if ($request->hasFile('return_photos')) {
            foreach ($request->file('return_photos') as $photo) {
                $photoPaths[] = $photo->store('vehicle-returns/' . $log->id, 'public');
            }
        }

        $log->update([
            'return_time'          => $request->return_time,
            'end_odometer'         => $request->end_odometer,
            'fuel_level_end'       => $request->fuel_level_end,
            'condition_on_checkin' => $request->condition_on_checkin,
            'notes'                => $request->notes,
            'return_photos'        => !empty($photoPaths) ? $photoPaths : null,
            'checkin_doc_number'   => $docNumber,
        ]);

        $log->asset()->update(['current_status' => 'Tersedia']);

        return redirect()->route('user.kendaraan.index')
            ->with('success', "Kendaraan \"{$log->asset->name}\" berhasil dikembalikan. No. Dokumen: {$docNumber}");
    }

    /**
     * Download BAST kendaraan (checkout atau checkin).
     */
    public function downloadBast(VehicleLog $log, string $type)
    {
        if ($log->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $docNumber = ($type === 'checkin') ? $log->checkin_doc_number : $log->checkout_doc_number;

        if (!$docNumber) {
            return back()->with('error', 'Dokumen belum tersedia.');
        }

        $isCheckin = ($type === 'checkin');
        $title     = $isCheckin
            ? 'Berita Acara Pengembalian Kendaraan Dinas'
            : 'Berita Acara Penggunaan Kendaraan Dinas';

        $verificationUrl = route('public.verify', $docNumber);
        $options  = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => true, 'scale' => 5]);
        $qrCode   = (new QRCode($options))->render($verificationUrl);

        $asset      = $log->asset()->with('personInCharge')->first();
        $employee   = $log->employee;
        $headmaster = Employee::where('position', 'Kepala Sekolah')->first();

        $pdf = Pdf::loadView('vehicle-logs.bast-pdf', compact(
            'log', 'asset', 'employee', 'headmaster', 'title', 'isCheckin', 'qrCode'
        ));

        return $pdf->download(str_replace('/', '-', $docNumber) . '.pdf');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function generateDocumentNumber(string $type): string
    {
        $year       = date('Y');
        $month      = date('m');
        $romanMonth = $this->toRoman($month);

        $counts = [
            \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count(),
            \App\Models\AssetAssignment::whereYear('created_at', $year)->whereNotNull('return_doc_number')->count(),
            \App\Models\AssetInspection::whereYear('created_at', $year)->whereNotNull('inspection_doc_number')->count(),
            VehicleLog::whereYear('created_at', $year)->whereNotNull('checkout_doc_number')->count(),
            VehicleLog::whereYear('created_at', $year)->whereNotNull('checkin_doc_number')->count(),
        ];

        $sequence = sprintf('%04d', array_sum($counts) + 1);

        return "{$type}/SMKTL/{$romanMonth}/{$year}/{$sequence}";
    }

    private function toRoman(int $number): string
    {
        $map = ['M' => 1000,'CM' => 900,'D' => 500,'CD' => 400,'C' => 100,'XC' => 90,
                'L' => 50,'XL' => 40,'X' => 10,'IX' => 9,'V' => 5,'IV' => 4,'I' => 1];
        $result = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) { $number -= $int; $result .= $roman; break; }
            }
        }
        return $result;
    }
}
