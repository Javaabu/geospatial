<?php

namespace Javaabu\Geospatial\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;
use Javaabu\Geospatial\Tests\TestCase;
use Javaabu\Geospatial\Tests\TestSupport\Models\City;

class HasSpatialSqliteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        RefreshDatabaseState::$migrated = false;

        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';

        parent::setUp();
    }

    /** @test */
    public function it_can_set_polygon_for_sqlite(): void
    {
        $city = new City();
        $city->name = 'Male City';
        $wkt = '(73.50924692977462 4.175893831117514,73.50942707022546 4.175893831117514,73.50942707022546 4.175714168882511,73.50924692977462 4.175714168882511,73.50924692977462 4.175893831117514)';
        $city->setPolygon($wkt);

        $city->save();

        $this->assertDatabaseHas('cities', [
            'name' => 'Male City',
            'boundary' => $city->toTestDbString(Polygon::fromWkt($wkt, 4326)),
        ]);
    }

    /** @test */
    public function it_can_get_polygon_for_sqlite(): void
    {
        $city = new City();
        $city->name = 'Male City';
        $wkt = 'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))';
        $city->setPolygon($wkt);

        $city->save();
        $city->refresh();

        $this->assertEquals($wkt, $city->boundary->toWkt());
    }

    /** @test */
    public function it_can_set_coordinates_for_sqlite(): void
    {
        $city = new City();
        $latitude = 4.175804;
        $longitude = 73.509337;
        $city->name = 'Male City';
        $city->setPoint($latitude, $longitude);

        $city->save();

        $this->assertDatabaseHas('cities', [
            'name' => 'Male City',
            'coordinates' => $city->toTestDbString(new Point($latitude, $longitude, 4326)),
        ]);
    }

    /** @test */
    public function it_can_get_coordinates_for_sqlite(): void
    {
        $city = new City();
        $latitude = 4.175804;
        $longitude = 73.509337;
        $city->name = 'Male City';
        $city->setPoint($latitude, $longitude);

        $city->save();

        $city->refresh();

        $this->assertEquals($latitude, $city->lat);
    }
}
