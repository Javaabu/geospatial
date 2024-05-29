<?php

namespace Javaabu\Geospatial\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Javaabu\Geospatial\HasSpatial;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;

class City extends Model
{
    use HasSpatial;

    protected $casts = [
        'coordinates' => Point::class,
        'boundary' => Polygon::class,
    ];
}
