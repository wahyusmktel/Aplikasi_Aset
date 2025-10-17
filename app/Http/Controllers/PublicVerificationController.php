<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    public function verify(string $docNumber)
    {
        $assignment = AssetAssignment::where('checkout_doc_number', $docNumber)
            ->orWhere('return_doc_number', $docNumber)
            ->with(['asset', 'employee'])
            ->firstOrFail();

        $isReturn = ($assignment->return_doc_number === $docNumber);
        $documentType = $isReturn ? 'Berita Acara Pengembalian' : 'Berita Acara Serah Terima';

        return view('public.verification', compact('assignment', 'documentType', 'isReturn'));
    }
}
