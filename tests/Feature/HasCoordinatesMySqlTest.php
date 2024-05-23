<?php

namespace Javaabu\Geospatial\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Tests\TestCase;
use Javaabu\Geospatial\Tests\TestSupport\Models\City;
use MatanYadaev\EloquentSpatial\Enums\Srid;

class HasCoordinatesMySqlTest extends TestCase
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
            'coordinates' => $city->toTestDbString(new Point($latitude, $longitude, Srid::WGS84)),
        ]);
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
