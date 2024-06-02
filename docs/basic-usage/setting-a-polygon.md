---
title: Setting A Polygon
sidebar_position: 3
---

### Setting a Polygon Column

The package supports using a `wkt` string to define the points of the polygon.

:::info

Wkt should be a valid wkt format string.

Example: POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))

Optionally, you can also omit the word "POLYGON" from the wkt string.

:::

You also have the option to manually pass in the database `column` and `srid` as the second argument and third argument.

```php
$city = new City();
$city->name = 'Male City';
$wkt = '(73.50924692977462 4.175893831117514,73.50942707022546 4.175893831117514,73.50942707022546 4.175714168882511,73.50924692977462 4.175714168882511,73.50924692977462 4.175893831117514)';
$city->setPolygon($wkt); // // $city->setPolygon($wkt, 'coordinates', 4326);

$city->save();
```
