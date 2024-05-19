<?php
/**
 * Simple trait for coordinates posts
 *
 * User: Arushad
 * Date: 06/10/2016
 * Time: 16:28
 */

namespace Javaabu\Geospatial;

use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Javaabu\Geospatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

trait HasCoordinates
{
    use HasSpatial;

    /**
     * Get the latitude
     *
     * @param null $value
     * @return float
     */
    public function getLatAttribute($value = null)
    {
        return is_null($value) ? optional($this->coordinates)->latitude : $value;
    }

    /**
     * Get the longitude
     *
     * @param null $value
     * @return float
     */
    public function getLngAttribute($value = null)
    {
        return is_null($value) ? optional($this->coordinates)->longitude : $value;
    }

    /**
     * Update coords
     *
     * @param float $lat
     * @param float $lng
     * @return void
     */
    public function setCoordinates($lat, $lng, $srid = Srid::WGS84)
    {
        $this->coordinates = new Point($lat, $lng, $srid);
    }

    /**
     * Within bounds
     *
     * @param $query
     * @param $bounds
     * @return mixed
     */
    public function scopeWithinBounds($query, $bounds)
    {
        if (! $bounds instanceof Polygon) {
            $bounds = Polygon::fromWKT($bounds);
        }

        return $query->within('coordinates', $bounds);
    }

    public function toTestDbString(Geometry $geometry)
    {
        return $geometry->toSqlExpression($this->getConnection());
    }
}
