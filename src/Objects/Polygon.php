<?php

namespace Javaabu\Geospatial\Objects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Javaabu\Geospatial\Factory;
use Javaabu\Geospatial\GeometryCast;
use Javaabu\Geospatial\Geospatial;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\GeometryExpression;

class Polygon extends \MatanYadaev\EloquentSpatial\Objects\Polygon
{
    public function toSqlExpression(ConnectionInterface $connection): ExpressionContract
    {
        if (Geospatial::supported($connection)) {
            return parent::toSqlExpression($connection);
        }

        $wkt = $this->toWkt();

        return DB::raw('"' . (new GeometryExpression("ST_GeomFromText('{$wkt}', {$this->srid})"))->normalize($connection) . '"');
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new GeometryCast(static::class);
    }

    public static function fromWkt(string $wkt, int|Srid|null $srid = 0): static
    {
        $wkt = Str::upper($wkt);

        if (! Str::startsWith($wkt, 'POLYGON')) {
            $wkt = "POLYGON ($wkt)";
        }

        $geometry = Factory::parse($wkt);
        $geometry->srid = $srid instanceof Srid ? $srid->value : $srid;

        if (! ($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometry::class)
            );
        }

        return $geometry;
    }

    public static function fromWkb(string $wkb): static
    {
        if (ctype_xdigit($wkb)) {
            // @codeCoverageIgnoreStart
            $geometry = Factory::parse($wkb);
            // @codeCoverageIgnoreEnd
        } else {
            $srid = substr($wkb, 0, 4);
            // @phpstan-ignore-next-line
            $srid = unpack('L', $srid)[1];

            $wkb = substr($wkb, 4);

            $geometry = Factory::parse($wkb);
            $geometry->srid = $srid;
        }

        if (! ($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometry::class)
            );
        }

        return $geometry;
    }
}
