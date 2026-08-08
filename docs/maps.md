# Weather Maps

Weather Maps API 1.0 provides current cloud, precipitation, sea-level pressure,
wind-speed, and temperature overlays. It is available on OpenWeather's standard
free and paid subscriptions. See the
[official Weather Maps documentation](https://openweathermap.org/api/weathermaps)
for API details.

## Generate A Tile URL

Use `tileUrl()` to generate an authenticated URL for a mapping library, image,
or other client that loads the tile directly.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$url = $api->maps()->tileUrl(
    layer: MapLayer::PRECIPITATION,
    zoom: 6,
    x: 31,
    y: 20,
);
```

Generating the URL does not make an HTTP request. It contains the API key passed
to `OpenWeatherMap`, so treat it as a credential and avoid including it in logs
or other unintended output.

## Fetch A Tile

Use `tile()` with a layer, zoom level, and X and Y tile coordinates.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$tile = $api->maps()->tile(
    layer: MapLayer::PRECIPITATION,
    zoom: 6,
    x: 31,
    y: 20,
);
```

`tile()` returns one 256-by-256 PNG overlay as a `MapTile`. The request is made
by the PHP application, so the API key is not included in the returned value.

```php
header('Content-Type: ' . $tile->contentType());

echo $tile->contents();
```

## Tile Coordinates

X and Y are tile indexes, not longitude and latitude. Weather Maps uses the
same square-tile grid as common web mapping libraries:

- Zoom 0 contains one tile representing the whole world: X 0, Y 0.
- Each additional zoom level doubles the number of tiles along each axis.
- X increases from west to east.
- Y increases from north to south.

For example:

| Zoom | Tile grid | Valid X and Y values |
|---:|---:|---:|
| 0 | 1 × 1 | 0 |
| 1 | 2 × 2 | 0–1 |
| 2 | 4 × 4 | 0–3 |
| 6 | 64 × 64 | 0–63 |

Zoom must be zero or greater. At any zoom level, the largest valid X or Y value
is `(2 ** $zoom) - 1`. Mapping libraries normally calculate these indexes from
the displayed geographic area; they should not be replaced directly with a
location's longitude and latitude.

Applications displaying Weather Maps data must provide visible OpenWeather
attribution. Consult the
[official FAQ](https://openweathermap.org/faq)
for the current attribution requirements.
