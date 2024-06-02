---
title: Search For Points Within a Polygon Boundary
sidebar_position: 4
---

### Search For Points Within a Polygon Boundary

The trait provides a `scopeWithinBounds` scope to search for records that fall within a given boundary.

```php
$city = new City();
$city->name = 'Male City';
$city->setPoint(4.175804, 73.509337);
$city->save();

$male_city_boundary_wkt = 'POLYGON ((73.50932514628285 4.175929944808645,73.50954911073559 4.175730219415812,73.50914768804103 4.17570881870468,73.50932514628285 4.175929944808645))';

$cities_within_search_area = City::withinBounds($wkt)->get();
$cities_within_search_area->first()->name; // "Male City"

$uligan_boundary_wkt = '(72.92683934689452 7.0841231111032235,72.92706331134727 7.083924382773967,72.9266618886527 7.083903088896789,72.92683934689452 7.0841231111032235)';
City::withinBounds($in_uligan)->first(); // null
```

