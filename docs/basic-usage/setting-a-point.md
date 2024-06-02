---
title: Setting A Point
sidebar_position: 2
---

### Setting a Point Column

Use the `setPoint` method. Method accepts a latitude and longitude. You also have the option to manually pass in the database `column` and `srid` as the third argument and fourth argument.
```php
$city = new City();
$latitude = 4.175804;
$longitude = 73.509337;
$city->name = 'Male City';
$city->setPoint($latitude, $longitude); // $city->setPoint($latitude, $longitude, 'coordinates', 4326);
$city->save();

$city->lat // 4.175804
$city->lng // 73.509337
```
You can use the `lat` and `lng` attributes to get the latitude and longitude of the point.
