<?php

namespace Javaabu\Geospatial;

use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\GeometryExpression;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

class GeometryCast extends \MatanYadaev\EloquentSpatial\GeometryCast
{
    protected string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
        parent::__construct($className);
    }

    /**
     * @param  Model  $model
     * @param  string|ExpressionContract|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get($model, string $key, $value, array $attributes): ?Geometry
    {
        if (Geospatial::supported($model->getConnection())) {
            return parent::get($model, $key, $value, $attributes);
        }

        if (! $value) {
            return null;
        }

        if (! $value instanceof ExpressionContract) {
            $value = DB::raw((new GeometryExpression($value))->normalize($model->getConnection()));
        }

        $wkt = $this->extractWktFromExpression($value, $model->getConnection());
        $srid = $this->extractSridFromExpression($value, $model->getConnection());

        return $this->className::fromWkt($wkt, $srid);
    }

    protected function extractWktFromExpression(ExpressionContract $expression, Connection $connection): string
    {
        $grammar = $connection->getQueryGrammar();
        $expressionValue = $expression->getValue($grammar);

        preg_match('/ST_GeomFromText\(\'(.+)\', .+(, .+)?\)/', (string) $expressionValue, $match);

        return $match[1];
    }

    protected function extractSridFromExpression(ExpressionContract $expression, Connection $connection): int
    {
        $grammar = $connection->getQueryGrammar();
        $expressionValue = $expression->getValue($grammar);

        preg_match('/ST_GeomFromText\(\'.+\', (.+)(, .+)?\)/', (string) $expressionValue, $match);

        return (int) $match[1];
    }

}
