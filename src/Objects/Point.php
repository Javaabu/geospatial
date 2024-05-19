<?php

namespace Javaabu\Geospatial\Objects;

use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Javaabu\Geospatial\GeometryCast;
use Javaabu\Geospatial\Geospatial;
use MatanYadaev\EloquentSpatial\GeometryExpression;

class Point extends \MatanYadaev\EloquentSpatial\Objects\Point
{
    public function toSqlExpression(ConnectionInterface $connection): ExpressionContract
    {
        if (Geospatial::supported($connection)) {
            return parent::toSqlExpression($connection);
        }

        $wkt = $this->toWkt();

        return DB::raw('"' . (new GeometryExpression("ST_GeomFromText('{$wkt}', {$this->srid})"))->normalize($connection) . '"');
    }
}
