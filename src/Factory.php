<?php

namespace Javaabu\Geospatial;

use Brick\Geo\Geometry as BrickGeometry;
use Brick\Geo\MultiPoint;
use InvalidArgumentException;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

class Factory extends \MatanYadaev\EloquentSpatial\Factory
{
    public static function parse(string $value): Geometry
    {
        return parent::parse($value);
    }

    protected static function createFromGeometry(BrickGeometry $geometry, bool $isRoot = true): Geometry
    {
        $srid = is_int($geometry->getSRID()) ? $geometry->getSRID() : 0;

        if ($geometry instanceof Point) {
            if ($geometry->coords[0] === null || $geometry->coords[1] === null) {
                throw new InvalidArgumentException('Invalid spatial value');
            }

            return new Point($geometry->coords[1], $geometry->coords[0], $srid);
        }

        $components = collect($geometry->components)
            ->map(static function (MultiPoint $geometryComponent): Geometry {
                return self::createFromGeometry($geometryComponent);
            });

        if ($geometry::class === Polygon::class) {
            return new Polygon($components, $srid);
        }

        return parent::createFromGeometry($geometry, $isRoot);
    }
}
