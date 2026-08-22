<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Lab;
use App\Models\LabAsset;
use App\Models\PersonInCharge;
use App\Models\Room;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class LabTest extends TestCase
{
    public function test_it_defines_inventory_attributes_and_relationships(): void
    {
        $lab = new Lab;

        $this->assertSame([
            'name',
            'room_id',
            'department_id',
            'person_in_charge_id',
        ], $lab->getFillable());

        $this->assertBelongsTo($lab->room(), Room::class);
        $this->assertBelongsTo($lab->department(), Department::class);
        $this->assertBelongsTo($lab->personInCharge(), PersonInCharge::class);

        $labAssets = $lab->labAssets();
        $this->assertInstanceOf(HasMany::class, $labAssets);
        $this->assertSame(LabAsset::class, $labAssets->getRelated()::class);
    }

    private function assertBelongsTo(BelongsTo $relation, string $relatedClass): void
    {
        $this->assertSame($relatedClass, $relation->getRelated()::class);
    }
}
