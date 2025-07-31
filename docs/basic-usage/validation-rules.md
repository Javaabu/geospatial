---
title: Validation Rules
sidebar_position: 5
---

This package provides the following validation rules to validate WKT Polygons.

## wkt_geo_polygon

```php
// in your validation class
public function rules(): array
{
    return [
        'boundary' => 'wkt_geo_polygon'
    ]
}
```

The validation rule checks for the following things:
- Must have more than 2 points
- Start and end points must be the same
- Each point must be a valid coordinate
- Allows with and without the `POLYGON` prefix

```php
'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))' // passes
'(73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758)' // passes

'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5032 4.1718))' // fails because start and end don't match
'POLYGON((73.5092 4.1758, 73.5092 4.1758))' // fails because only 2 points
'POLYGON((73.5092 4.1758, 73.5094 a4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))' // fails due to invalid point values
'POLYGON((73.5092 4.1758, 73.5094 14324.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))' // fails due to out of bounds coordinates
```
