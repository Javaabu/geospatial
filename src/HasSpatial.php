<?php
/**
 * Simple trait for coordinates posts
 *
 * User: Arushad
 * Date: 06/10/2016
 * Time: 16:28
 */

namespace Javaabu\Geospatial;

use Javaabu\Geospatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial as EloquentHasSpatial;
use Javaabu\Geospatial\Objects\Point;

trait HasSpatial
{
    use EloquentHasSpatial;

    /**
     * Get the latitude
     *
     * @param null $value
     * @return float
     */
    public function getLatAttribute($value = null)
    {
        $column = $this->getDefaultPointField();

        return is_null($value) ? optional($this->{$column})->latitude : $value;
    }

    /**
     * Get the longitude
     *
     * @param null $value
     * @return float
     */
    public function getLngAttribute($value = null)
    {
        $column = $this->getDefaultPointField();

        return is_null($value) ? optional($this->{$column})->longitude : $value;
    }

    public function getDefaultPointField(): string
    {
        return 'coordinates';
    }

    public function getDefaultPolygonField(): string
    {
        return 'boundary';
    }

    public function setPoint($lat, $lng, string $column = '', $srid = Srid::WGS84)
    {
        if (! $column) {
            $column = $this->getDefaultPointField();
        }

        $this->{$column} = new Point($lat, $lng, $srid);
    }

    public function setPolygon(string $wkt, string $column = '', $srid = Srid::WGS84) {
        if (! $column) {
            $column = $this->getDefaultPolygonField();
        }

        $this->{$column} = Polygon::fromWkt($wkt, $srid);
    }

    /**
     * Within bounds
     *
     * @param $query
     * @param $bounds
     * @return mixed
     */
    public function scopeWithinBounds($query, $bounds, string $column = '', $srid = Srid::WGS84)
    {
        if (! $bounds instanceof Polygon) {
            $bounds = Polygon::fromWKT($bounds, $srid);
        }

        if (! $column) {
            $column = $this->getDefaultPointField();
        }

        return $query->whereWithin($column, $bounds);
    }

    public function toTestDbString(Geometry $geometry)
    {
        return $geometry->toSqlExpression($this->getConnection());
    }
}
