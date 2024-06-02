---
title: Preparing The Model
sidebar_position: 1
---

### Preparing The Model

This is a sample migration for a `City` model as an example.

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
            $table->geography('coordinates', 'point')->nullable();
            $table->geography('boundary', 'polygon')->nullable();
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
