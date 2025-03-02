<?php

namespace Javaabu\Geospatial\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;
use Javaabu\Geospatial\Tests\TestCase;
use Javaabu\Geospatial\Tests\TestSupport\Models\City;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use PHPUnit\Framework\Attributes\Test;

class ValidationRulesTest extends TestCase
{

    #[Test]
    public function it_can_validate_wkt_geo_polygon(): void
    {
        $wkt = 'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_can_validate_wkt_geo_polygon_without_polygon_prefix(): void
    {
        $wkt = '(73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758)';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_checks_if_start_and_end_values_are_same(): void
    {
        $wkt = 'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5032 4.1718))';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertTrue($validator->fails());
    }

    #[Test]
    public function it_checks_if_the_polygon_has_more_than_two_points(): void
    {
        $wkt = 'POLYGON((73.5092 4.1758, 73.5092 4.1758))';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertTrue($validator->fails());
    }

    #[Test]
    public function it_does_not_accept_invalid_point_values(): void
    {
        $wkt = 'POLYGON((73.5092 4.1758, 73.5094 a4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertTrue($validator->fails());
    }

    #[Test]
    public function it_does_not_accept_invalid_coordinate_values(): void
    {
        $wkt = 'POLYGON((73.5092 4.1758, 73.5094 14324.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))';
        $validator = validator(compact('wkt'), ['wkt' => 'wkt_geo_polygon']);

        $this->assertTrue($validator->fails());
    }
}
