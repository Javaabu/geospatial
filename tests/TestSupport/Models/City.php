<?php

namespace Javaabu\Geospatial\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Javaabu\Geospatial\HasCoordinates;
use Javaabu\Geospatial\Objects\Point;

class City extends Model
{
    use HasCoordinates;

    protected $casts = [
        'coordinates' => Point::class,
    ];
}
