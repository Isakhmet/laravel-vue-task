<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'Qliq',
        'Qoil',
        'company_id',
        'date'
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
}
