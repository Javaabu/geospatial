<?php

namespace Javaabu\Geospatial\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;
use Javaabu\Geospatial\Tests\TestCase;
use Javaabu\Geospatial\Tests\TestSupport\Models\City;

class HasSpatialMySqlTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        RefreshDatabaseState::$migrated = false;

        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_DATABASE'] = 'geospatial';

        parent::setUp();
    }

    /** @test */
    public function it_can_search_within_a_polygon_for_mysql(): void
    {
        $city = new City();
        $city->name = 'Male City';
        $city->setPoint(4.175804, 73.509337);
        $wkt = 'POLYGON ((73.50932514628285 4.175929944808645,73.50954911073559 4.175730219415812,73.50914768804103 4.17570881870468,73.50932514628285 4.175929944808645))';

        $city->save();

        $this->assertEquals('Male City', City::withinBounds($wkt)->first()->name);

        $in_uligan = '(72.92683934689452 7.0841231111032235,72.92706331134727 7.083924382773967,72.9266618886527 7.083903088896789,72.92683934689452 7.0841231111032235)';
        $this->assertNull(City::withinBounds($in_uligan)->first());
    }

    /** @test */
    public function it_can_set_polygon_for_mysql(): void
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
    public function it_can_get_polygon_for_mysql(): void
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
    public function it_can_set_coordinates_for_mysql(): void
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
    public function it_can_get_coordinates_for_mysql(): void
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

    /** @test */
    public function it_generates_lat_from_the_default_point_column(): void
    {
        $city = new City();
        $latitude = 4.175804;
        $longitude = 73.509337;
        $city->name = 'Male City';
        $city->setPoint($latitude, $longitude);

        $city->save();

        $this->assertEquals($latitude, $city->lat);
    }

    /** @test */
    public function it_generates_lng_from_the_default_point_column(): void
    {
        $city = new City();
        $latitude = 4.175804;
        $longitude = 73.509337;
        $city->name = 'Male City';
        $city->setPoint($latitude, $longitude);

        $city->save();

        $this->assertEquals($longitude, $city->lng);
    }

}
