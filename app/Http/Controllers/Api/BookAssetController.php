<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAssetStoreRequest;
use App\Http\Requests\BookAssetUpdateRequest;
use App\Http\Resources\BookAssetResource;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Building;
use App\Models\Room;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\PersonInCharge;
use App\Models\AssetFunction;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookAssetController extends Controller
{
    protected function bookCategoryId(): int
    {
        if ($id = config('assets.book_category_id')) {
            return (int) $id;
        }
        $cat = Category::where('name', 'like', '%buku%')->first();
        abort_if(!$cat, 422, 'Kategori "Buku" tidak ditemukan. Set BOOK_CATEGORY_ID di .env.');
        return (int) $cat->id;
    }

    // GET /api/books
    public function index(Request $request)
    {
        $q      = trim((string)$request->get('q'));
        $year   = $request->get('year');
        $status = $request->get('status'); // boleh array/string
        $sort   = $request->get('sort', 'asset_code_ypt'); // name|year|code
        $dir    = strtolower($request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $bookCatId = $this->bookCategoryId();

        $query = Asset::query()
            ->where('category_id', $bookCatId)
            ->with(['building', 'room', 'personInCharge'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('asset_code_ypt', 'like', "%{$q}%");
                });
            })
            ->when(!empty($year), fn($qq) => $qq->where('purchase_year', $year))
            ->when(!empty($status), function ($qq) use ($status) {
                is_array($status) ? $qq->whereIn('status', $status) : $qq->where('status', $status);
            });

        // sort mapping
        $map = [
            'name'            => 'name',
            'year'            => 'purchase_year',
            'code'            => 'asset_code_ypt',
            'asset_code_ypt'  => 'asset_code_ypt',
        ];
        $query->orderBy($map[$sort] ?? 'asset_code_ypt', $dir);

        $perPage = min(max((int)$request->get('per_page', 20), 1), 200);
        return BookAssetResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    // GET /api/books/{id}
    public function show($id)
    {
        $bookCatId = $this->bookCategoryId();
        $asset = Asset::where('category_id', $bookCatId)
            ->with(['institution', 'category', 'building', 'room', 'faculty', 'department', 'personInCharge', 'assetFunction', 'fundingSource'])
            ->findOrFail($id);

        return new BookAssetResource($asset);
    }

    // POST /api/books
    public function store(BookAssetStoreRequest $request)
    {
        $bookCatId = $this->bookCategoryId();

        // Sequence number auto increment (ikut logika yang sudah ada)
        $latest = Asset::orderBy('id', 'desc')->first();
        $newSeq = $latest ? intval($latest->sequence_number) + 1 : 1;
        $seq4   = sprintf('%04d', $newSeq);

        $payload = array_merge($request->validated(), [
            'category_id'     => $bookCatId, // force kategori buku
            'sequence_number' => $seq4,
        ]);

        DB::transaction(function () use (&$asset, $payload) {
            $asset = Asset::create($payload);

            // Generate Kode Aset YPT (samakan pola dari AssetController@store)
            $institution     = Institution::find($asset->institution_id);
            $year2           = substr($asset->purchase_year, -2);
            $category        = Category::find($asset->category_id);
            $building        = $asset->building_id       ? Building::find($asset->building_id) : null;
            $room            = $asset->room_id           ? Room::find($asset->room_id) : null;
            $faculty         = $asset->faculty_id        ? Faculty::find($asset->faculty_id) : null;
            $department      = $asset->department_id     ? Department::find($asset->department_id) : null;
            $personInCharge  = $asset->person_in_charge_id ? PersonInCharge::find($asset->person_in_charge_id) : null;
            $assetFunction   = $asset->asset_function_id ? AssetFunction::find($asset->asset_function_id) : null;
            $fundingSource   = $asset->funding_source_id ? FundingSource::find($asset->funding_source_id) : null;

            $asset_code_ypt = implode('.', array_filter([
                optional($institution)->code,
                $year2,
                optional($category)->code,
                optional($building)->code,
                optional($room)->code,
                optional($faculty)->code,
                optional($department)->code,
                optional($personInCharge)->code,
                optional($assetFunction)->code,
                optional($fundingSource)->code,
                $asset->sequence_number
            ]));

            $asset->update([
                'asset_code_ypt' => $asset_code_ypt,
                'status'         => $payload['status'] ?? 'Aktif',
            ]);
        });

        return (new BookAssetResource($asset))->response()->setStatusCode(201);
    }

    // PUT/PATCH /api/books/{id}
    public function update(BookAssetUpdateRequest $request, $id)
    {
        $bookCatId = $this->bookCategoryId();

        $asset = Asset::where('category_id', $bookCatId)->findOrFail($id);
        $asset->update($request->validated());

        // Jika field yang mempengaruhi kode YPT berubah, regenerate (opsional)
        if ($request->hasAny([
            'institution_id',
            'purchase_year',
            'category_id',
            'building_id',
            'room_id',
            'faculty_id',
            'department_id',
            'person_in_charge_id',
            'asset_function_id',
            'funding_source_id'
        ])) {
            $institution     = Institution::find($asset->institution_id);
            $year2           = substr($asset->purchase_year, -2);
            $category        = Category::find($asset->category_id);
            $building        = $asset->building_id       ? Building::find($asset->building_id) : null;
            $room            = $asset->room_id           ? Room::find($asset->room_id) : null;
            $faculty         = $asset->faculty_id        ? Faculty::find($asset->faculty_id) : null;
            $department      = $asset->department_id     ? Department::find($asset->department_id) : null;
            $personInCharge  = $asset->person_in_charge_id ? PersonInCharge::find($asset->person_in_charge_id) : null;
            $assetFunction   = $asset->asset_function_id ? AssetFunction::find($asset->asset_function_id) : null;
            $fundingSource   = $asset->funding_source_id ? FundingSource::find($asset->funding_source_id) : null;

            $asset_code_ypt = implode('.', array_filter([
                optional($institution)->code,
                $year2,
                optional($category)->code,
                optional($building)->code,
                optional($room)->code,
                optional($faculty)->code,
                optional($department)->code,
                optional($personInCharge)->code,
                optional($assetFunction)->code,
                optional($fundingSource)->code,
                $asset->sequence_number
            ]));

            $asset->update(['asset_code_ypt' => $asset_code_ypt]);
        }

        return new BookAssetResource($asset);
    }

    // DELETE /api/books/{id}
    public function destroy($id)
    {
        $bookCatId = $this->bookCategoryId();
        $asset = Asset::where('category_id', $bookCatId)->findOrFail($id);
        $asset->delete();

        return response()->json(['message' => 'Deleted'], 204);
    }
}
