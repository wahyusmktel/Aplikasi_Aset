<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// ✅ PAKAI NAMESPACE YANG BENAR DARI PACKAGE
use Gemini;

class AssetMappingController extends Controller
{
    private ?int $uncategorizedId = null;
    private array $allCategoriesList = [];
    private $gemini = null; // instance client

    public function __construct()
    {
        // Ambil ID kategori "Belum Ditentukan" (boleh null kalau belum ada)
        $this->uncategorizedId = Category::where('name', 'Belum Ditentukan')->value('id');

        // Ambil semua kategori selain "Belum Ditentukan"
        $this->allCategoriesList = Category::when($this->uncategorizedId, function ($q) {
            $q->where('id', '!=', $this->uncategorizedId);
        })
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        // ✅ INIT GEMINI CLIENT SESUAI DOKUMENTASI
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                Log::error('GEMINI_API_KEY is not set in .env.');
                $this->gemini = null;
                return;
            }

            // Cara ringkas
            $this->gemini = Gemini::client($apiKey);

            // Atau kalau mau timeout custom:
            // $this->gemini = Gemini::factory()
            //     ->withApiKey($apiKey)
            //     ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 30]))
            //     ->make();

        } catch (\Throwable $e) {
            Log::error('Failed to initialize Gemini client: ' . $e->getMessage());
            $this->gemini = null;
        }
    }

    /**
     * Tampilkan halaman mapping: 10 aset belum berkategori + saran AI.
     */
    public function index()
    {
        $assetsToMap = Asset::when($this->uncategorizedId, function ($q) {
            $q->where('category_id', $this->uncategorizedId);
        }, function ($q) {
            $q->whereNull('category_id');
        })
            ->orWhereNull('category_id')
            ->orderBy('id')
            ->take(10)
            ->get();

        // Kalau client gagal inisialisasi, beri pesan di tiap aset
        if (!$this->gemini) {
            foreach ($assetsToMap as $asset) {
                $asset->ai_suggestion_type = 'error';
                $asset->ai_suggestion = 'AI tidak aktif: cek GEMINI_API_KEY atau log.';
            }

            return view('asset-mapping.index', [
                'assetsToMap'   => $assetsToMap,
                'allCategories' => Category::when($this->uncategorizedId, fn($q) => $q->where('id', '!=', $this->uncategorizedId))
                    ->orderBy('name')->get(),
            ]);
        }

        $categoriesString = implode(', ', $this->allCategoriesList);

        foreach ($assetsToMap as $asset) {
            $prompt = <<<PROMPT
Ini adalah nama aset: "{$asset->name}".

Ini daftar kategori yang SUDAH ADA di sistem:
[$categoriesString]

Tugasmu:
1) Jika salah satu kategori di daftar cocok, pilih SATU yang paling cocok.
2) Jika TIDAK ADA yang cocok, berikan SATU nama kategori BARU (bahasa Indonesia, singkat & spesifik).

Jawab HANYA teks nama kategori (tanpa penjelasan lain).
PROMPT;

            try {
                // ✅ Panggil model sesuai docs
                $response   = $this->gemini->generativeModel(model: 'gemini-2.0-flash')
                    ->generateContent($prompt);
                $suggestion = trim((string) $response->text());
                // Bersihkan karakter yang sering "nyangkut"
                $suggestion = trim($suggestion, " \n\r\t\v\0\"'*.,");

                if ($suggestion === '') {
                    $asset->ai_suggestion_type = 'error';
                    $asset->ai_suggestion = 'Tidak ada saran dari AI.';
                    continue;
                }

                if (in_array($suggestion, $this->allCategoriesList, true)) {
                    $asset->ai_suggestion_type = 'existing';
                    $asset->ai_suggestion = $suggestion;
                } else {
                    $asset->ai_suggestion_type = 'new';
                    $asset->ai_suggestion = $suggestion;
                }
            } catch (\Throwable $e) {
                Log::error('Gemini API error: ' . $e->getMessage());
                $asset->ai_suggestion_type = 'error';
                $asset->ai_suggestion = 'Gagal mendapat rekomendasi AI.';
            }
        }

        return view('asset-mapping.index', [
            'assetsToMap'   => $assetsToMap,
            'allCategories' => Category::when($this->uncategorizedId, fn($q) => $q->where('id', '!=', $this->uncategorizedId))
                ->orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan mapping kategori massal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_category'   => 'required|array',
            'asset_category.*' => 'required|integer|exists:categories,id',
        ]);

        $updated = 0;
        foreach ($request->asset_category as $assetId => $categoryId) {
            $asset = Asset::find($assetId);
            if ($asset && $categoryId) {
                $asset->update([
                    'category_id' => $categoryId,
                ]);
                $updated++;
            }
        }

        alert()->success('Berhasil!', "{$updated} aset berhasil di-mapping ulang.");
        return redirect()->route('asset-mapping.index');
    }
}
