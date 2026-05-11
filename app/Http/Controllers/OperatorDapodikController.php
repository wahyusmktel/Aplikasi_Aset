<?php

namespace App\Http\Controllers;

use App\Models\DapodikChangeRequest;
use Illuminate\Http\Request;

class OperatorDapodikController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = DapodikChangeRequest::with(['employee', 'requestedBy'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = DapodikChangeRequest::where('status', 'pending')->count();

        return view('operator.dapodik.index', compact('requests', 'status', 'pendingCount'));
    }

    public function show(DapodikChangeRequest $changeRequest)
    {
        $changeRequest->load(['employee', 'requestedBy', 'reviewedBy']);
        return view('operator.dapodik.show', compact('changeRequest'));
    }

    public function approve(DapodikChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        // Apply proposed changes to the employee record
        $changeRequest->employee->update($changeRequest->proposed_changes);

        $changeRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan disetujui dan data pegawai telah diperbarui.');
    }

    public function reject(Request $request, DapodikChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $changeRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak. Alasan telah dikirim ke pegawai.');
    }
}
