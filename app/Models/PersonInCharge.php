<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonInCharge extends Model
{
    use HasFactory;

    protected $table = 'persons_in_charge';

    protected $fillable = ['name', 'code'];
}
