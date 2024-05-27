<?php

namespace Javaabu\Geospatial\Objects;

use Geometry as geoPHPGeometry;
use geoPHP;
use InvalidArgumentException;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use Point as geoPHPPoint;

class Factory extends \MatanYadaev\EloquentSpatial\Factory
{
    public static function parse(string $value): Geometry
    {
        try {
            /** @var geoPHPGeometry|false $geoPHPGeometry */
            $geoPHPGeometry = geoPHP::load($value);
        } finally {
            if (! isset($geoPHPGeometry) || ! $geoPHPGeometry) {
                throw new InvalidArgumentException('Invalid spatial value');
            }
        }

        return self::createFromGeometry($geoPHPGeometry);
    }
    protected static function createFromGeometry(geoPHPGeometry $geometry): Geometry
    {
        $srid = is_int($geometry->getSRID()) ? $geometry->getSRID() : 0;

        if ($geometry instanceof geoPHPPoint) {
            if ($geometry->coords[0] === null || $geometry->coords[1] === null) {
                throw new InvalidArgumentException('Invalid spatial value');
            }

            return new Point($geometry->coords[1], $geometry->coords[0], $srid);
        }

        return parent::createFromGeometry($geometry);
    }


}
