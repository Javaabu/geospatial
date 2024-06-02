---
title: Testing
sidebar_position: 5
---

Since by default, sqlite doesn't support spatial columns, the `HasSpatial` trait adds a `toTestDbString` that supports testing the geospatial column for both MySQL and sqlite.

The method accepts either a `Polygon` or a `Point` object.

```php
/** @test */
public function it_can_set_polygon(): void
{
    $city = new City();
    $city->name = 'Male City';
    $wkt = '(73.50924692977462 4.175893831117514,73.50942707022546 4.175893831117514,73.50942707022546 4.175714168882511,73.50924692977462 4.175714168882511,73.50924692977462 4.175893831117514)';
    $city->setPolygon($wkt);

    $city->save();

    $this->assertDatabaseHas('cities', [
        'name' => 'Male City',
        'boundary' => $city->toTestDbString(Polygon::fromWkt($wkt, Srid::WGS84)),
    ]);
}

/** @test */
public function it_can_get_polygon(): void
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
public function it_can_set_coordinates(): void
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
public function it_can_get_coordinates(): void
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
```
