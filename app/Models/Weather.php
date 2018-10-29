<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weather extends Model
{
    use SoftDeletes;

    protected $table = 'iata_weather';

    protected $fillable = [
        'key',
        'name',
        'city',
        'country',
        'iata',
        'icao',
        'latitude',
        'longitude',
        'altitude',
        'timezone',
        'dst'
    ];

    protected $dates = ['deleted_at'];
}
