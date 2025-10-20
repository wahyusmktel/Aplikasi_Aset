<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process; // Gunakan Process component

class WebhookController extends Controller
{
    public function handleDeploy(Request $request)
    {
        // 1. Validasi Signature (SANGAT PENTING!)
        $githubSignature = $request->header('X-Hub-Signature-256');
        $secret = env('GITHUB_WEBHOOK_SECRET'); // Ambil secret dari .env

        if (!$secret || !$githubSignature) {
            Log::warning('Deployment webhook received without secret or signature.');
            abort(403, 'Signature missing.');
        }

        $payloadBody = $request->getContent();
        $calculatedSignature = 'sha256=' . hash_hmac('sha256', $payloadBody, $secret, false);

        if (!hash_equals($githubSignature, $calculatedSignature)) {
            Log::error('Deployment webhook signature validation failed.');
            abort(403, 'Invalid signature.');
        }

        // 2. Cek Event (misal: hanya pull jika push ke branch main)
        if ($request->header('X-GitHub-Event') !== 'push' || data_get($request->json('ref'), '') !== 'refs/heads/main') {
            Log::info('Deployment webhook ignored (not a push to main branch).');
            return response()->json(['message' => 'Ignored: Not a push to main branch.']);
        }

        // 3. Jalankan Skrip Deployment
        $projectPath = base_path(); // Path ke root proyek Laravel
        // Buat instance Process
        $process = new Process(['/var/www/deploy-script.sh', $projectPath]);
        $process->setWorkingDirectory($projectPath); // Set direktori kerja

        try {
            $process->mustRun(); // Jalankan skrip, lempar exception jika gagal
            Log::info('Deployment script executed successfully: ' . $process->getOutput());
            return response()->json(['message' => 'Deployment successful!']);
        } catch (\Exception $e) {
            Log::error('Deployment script failed: ' . $e->getMessage());
            return response()->json(['message' => 'Deployment failed! Check logs.'], 500);
        }
    }
}
