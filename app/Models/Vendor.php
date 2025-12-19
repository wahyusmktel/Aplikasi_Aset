<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address', 'phone', 'email', 'contact_person', 'npwp'];

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }
}
