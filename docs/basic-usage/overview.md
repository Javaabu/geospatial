---
title: Overview
---

### Preparing The Model

This is a sample migration for a `City` model as an example.

:::danger

Make sure the database geospatial column has `4326` as `SRID`.

:::

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->geometry('coordinates', 'point', 4326)->nullable();
            $table->geometry('boundary', 'polygon', 4326)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
```

After the package has been installed, you can use the `HasSpatial` Trait in your models that interacts with MySQL geospatial columns. The package supports sqlite testing for Point and Polygon geospatial subtypes.

Then add the `Point` and `Polygon` casts to your model attributes according to the column subtype.

:::info

Both casts use `4326` as the default `SRID` for the spatial column functions.

:::

```php
use Illuminate\Database\Eloquent\Model;
use Javaabu\Geospatial\HasSpatial;
use Javaabu\Geospatial\Objects\Point;
use Javaabu\Geospatial\Objects\Polygon;

class City extends Model
{
    use HasSpatial;

    protected $casts = [
        'coordinates' => Point::class,
        'boundary' => Polygon::class,
    ];
}

```

The default column names used by the trait for Point and Polygon columns are `coordinates` and `boundary` respectively. You can override the `getDefaultPointField` and `getDefaultPolygonField` methods to change the model column.

```php
public function getDefaultPointField(): string
{
    return 'coordinates';
}

public function getDefaultPolygonField(): string
{
    return 'boundary';
}
```

### Setting a Point Column

Use the `setPoint` method. Method accepts a latitude and longitude. You also have the option to manually pass in the column name as the third argument.
```php
$city = new City();
$latitude = 4.175804;
$longitude = 73.509337;
$city->name = 'Male City';
$city->setPoint($latitude, $longitude);
$city->save();

$city->lat // 4.175804
$city->lng // 73.509337
```
You can use the `lat` and `lng` attributes to get the latitude and longitude of the point.

### Setting a Polygon Column

The package supports using a `wkt` string to define the points of the polygon.

:::info

Wkt could either have the POLYGON string surrounding the points or not, the package adds the string POLYGON if it's not there.

$wkt = 'POLYGON((73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758))';

(OR)

$wkt = '(73.5092 4.1758, 73.5094 4.1758, 73.5094 4.1757, 73.5092 4.1757, 73.5092 4.1758)';

:::

```php
$city = new City();
$city->name = 'Male City';
$wkt = '(73.50924692977462 4.175893831117514,73.50942707022546 4.175893831117514,73.50942707022546 4.175714168882511,73.50924692977462 4.175714168882511,73.50924692977462 4.175893831117514)';
$city->setPolygon($wkt);

$city->save();
```

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

