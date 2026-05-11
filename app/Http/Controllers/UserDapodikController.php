<?php

namespace App\Http\Controllers;

use App\Models\DapodikChangeRequest;
use Illuminate\Http\Request;

class UserDapodikController extends Controller
{
    public static function fieldLabel(string $field): string
    {
        return [
            'jenis_kelamin'      => 'Jenis Kelamin',
            'tempat_lahir'       => 'Tempat Lahir',
            'tanggal_lahir'      => 'Tanggal Lahir',
            'agama'              => 'Agama',
            'status_perkawinan'  => 'Status Perkawinan',
            'nuptk'              => 'NUPTK',
            'status_kepegawaian' => 'Status Kepegawaian',
            'golongan_pangkat'   => 'Golongan/Pangkat',
            'tmt_pengangkatan'   => 'TMT Pengangkatan',
            'pendidikan_terakhir'=> 'Pendidikan Terakhir',
            'bidang_studi'       => 'Bidang Studi',
            'lembaga_pendidikan' => 'Lembaga Pendidikan',
            'alamat'             => 'Alamat',
            'no_hp'              => 'No. HP',
        ][$field] ?? $field;
    }

    public function index()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Data pegawai tidak ditemukan. Hubungi operator.');
        }

        $pendingRequest  = $employee->pendingChangeRequest()->first();
        $rejectedRequest = $employee->dapodikChangeRequests()
            ->where('status', 'rejected')
            ->latest()
            ->first();

        // Show rejected only if there's no current pending re-submission
        if ($pendingRequest) {
            $rejectedRequest = null;
        }

        return view('user.dapodik.index', compact('employee', 'pendingRequest', 'rejectedRequest'));
    }

    public function submitRequest(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Block if there's already a pending request
        if ($employee->pendingChangeRequest()->exists()) {
            return back()->with('error', 'Anda sudah memiliki pengajuan yang sedang menunggu persetujuan.');
        }

        $fields = [
            'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
            'status_perkawinan', 'nuptk', 'status_kepegawaian',
            'golongan_pangkat', 'tmt_pengangkatan', 'pendidikan_terakhir',
            'bidang_studi', 'lembaga_pendidikan', 'alamat', 'no_hp',
        ];

        $proposed  = [];
        $current   = [];
        $hasChange = false;

        foreach ($fields as $field) {
            $newVal  = $request->input($field);
            $oldVal  = $employee->$field instanceof \Carbon\Carbon
                ? $employee->$field->format('Y-m-d')
                : $employee->$field;

            if ((string) $newVal !== (string) ($oldVal ?? '')) {
                $hasChange       = true;
                $proposed[$field] = $newVal;
                $current[$field]  = $oldVal;
            }
        }

        if (!$hasChange) {
            return back()->with('error', 'Tidak ada perubahan data yang diajukan.');
        }

        DapodikChangeRequest::create([
            'employee_id'      => $employee->id,
            'requested_by'     => auth()->id(),
            'proposed_changes' => $proposed,
            'current_values'   => $current,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Pengajuan perubahan data berhasil dikirim dan sedang menunggu validasi operator.');
    }
}
