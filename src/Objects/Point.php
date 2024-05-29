<?php

namespace Javaabu\Geospatial\Objects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Javaabu\Geospatial\Factory;
use Javaabu\Geospatial\GeometryCast;
use Javaabu\Geospatial\Geospatial;

class Point extends \MatanYadaev\EloquentSpatial\Objects\Point
{
    public function toSqlExpression(ConnectionInterface $connection): Expression
    {
        if (Geospatial::supported($connection)) {
            return parent::toSqlExpression($connection);
        }

        $wkt = $this->toWkt();

        return DB::raw('"' . "ST_GeomFromText('{$wkt}', {$this->srid})" . '"');
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new GeometryCast(static::class);
    }

    public static function fromWkt(string $wkt, int $srid = 0): static
    {
        $geometry = Factory::parse($wkt);
        $geometry->srid = $srid;

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
