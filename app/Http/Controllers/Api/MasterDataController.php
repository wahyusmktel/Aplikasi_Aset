<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Building;
use App\Models\Room;
use App\Models\PersonInCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MasterDataController extends Controller
{
    // Format Select2: { results: [ {id, text} ], pagination: { more: bool } }
    private function formatSelect2($paginator, $mapFn)
    {
        return response()->json([
            'results' => $paginator->getCollection()->map($mapFn)->values(),
            'pagination' => ['more' => $paginator->currentPage() < $paginator->lastPage()],
        ]);
    }

    public function institutions(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = Institution::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return $this->formatSelect2($pg, fn($i) => ['id' => $i->id, 'text' => $i->name]);
    }

    public function buildings(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = Building::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return $this->formatSelect2($pg, fn($b) => ['id' => $b->id, 'text' => $b->name]);
    }

    public function rooms(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $buildingId = $request->get('building_id');
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = \App\Models\Room::query()
            // filter building_id HANYA kalau kolomnya ada & param dikirim
            ->when(
                $buildingId && Schema::hasColumn('rooms', 'building_id'),
                fn($qq) => $qq->where('building_id', $buildingId)
            )
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return response()->json([
            'results' => $pg->getCollection()->map(fn($r) => ['id' => $r->id, 'text' => $r->name])->values(),
            'pagination' => ['more' => $pg->currentPage() < $pg->lastPage()],
        ]);
    }

    public function persons(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = PersonInCharge::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return $this->formatSelect2($pg, fn($p) => ['id' => $p->id, 'text' => $p->name]);
    }

    public function faculties(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = \App\Models\Faculty::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return response()->json([
            'results' => $pg->getCollection()->map(fn($x) => ['id' => $x->id, 'text' => $x->name])->values(),
            'pagination' => ['more' => $pg->currentPage() < $pg->lastPage()],
        ]);
    }

    public function departments(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = \App\Models\Department::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return response()->json([
            'results' => $pg->getCollection()->map(fn($x) => ['id' => $x->id, 'text' => $x->name])->values(),
            'pagination' => ['more' => $pg->currentPage() < $pg->lastPage()],
        ]);
    }

    public function assetFunctions(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = \App\Models\AssetFunction::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return response()->json([
            'results' => $pg->getCollection()->map(fn($x) => ['id' => $x->id, 'text' => $x->name])->values(),
            'pagination' => ['more' => $pg->currentPage() < $pg->lastPage()],
        ]);
    }

    public function fundingSources(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $per = min(max((int)$request->get('per_page', 20), 1), 100);

        $pg = \App\Models\FundingSource::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($per);

        return response()->json([
            'results' => $pg->getCollection()->map(fn($x) => ['id' => $x->id, 'text' => $x->name])->values(),
            'pagination' => ['more' => $pg->currentPage() < $pg->lastPage()],
        ]);
    }
}
