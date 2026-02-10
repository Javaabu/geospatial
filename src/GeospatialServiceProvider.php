<?php

namespace Javaabu\Geospatial;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Enums\Srid;

class GeospatialServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerCustomValidationRules();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        \MatanYadaev\EloquentSpatial\EloquentSpatial::usePoint(Point::class);
        \MatanYadaev\EloquentSpatial\EloquentSpatial::usePolygon(Polygon::class);
    }


    protected function registerCustomValidationRules()
    {
        /**
         * Must be a valid wkt polygon with the last points matching the first point
         * Using only valid latitude and longitude values
         * Rule works as wkt_polygon:srid
         */
        Validator::extend('wkt_geo_polygon', function ($attribute, $value, $parameters) {
            $srid = $parameters[0] ?? Srid::WGS84;

            try {
                /** @var Polygon $polygon */
                $polygon = Polygon::fromWkt($value, $srid);

                $lines = $polygon->getCoordinates();
                $path = $lines[0] ?? null;
                $length = $path ? count($path) : 0;

                // make sure more than 2 points
                if ($length <= 2) {
                    return false;
                }

                // make sure first and last points same
                $first = $path[0] ?? null;
                $last = $path[$length - 1] ?? null;

                $ends_meet = ($first && $last) &&
                    $first[0] == $last[0] &&
                    $first[1] == $last[1];

                if (! $ends_meet) {
                    return false;
                }

                // check if all points a are valid coordinates
                foreach ($path as $point) {
                    // validate latitude
                    if ($point[1] > 90 || $point[1] < -90) {
                        return false;
                    }

                    // validate longitude
                    if ($point[0] > 180 || $point[0] < -180) {
                        return false;
                    }
                }

                return true;

            } catch (\Exception $e) {}

            return false;
        });
    }
}
