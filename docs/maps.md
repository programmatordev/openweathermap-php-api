# Maps

Weather Maps API 1.0 provides current cloud, precipitation, sea-level pressure,
wind-speed, and temperature overlays. See the
[official Weather Maps documentation](https://openweathermap.org/api/weathermaps)
for API details.

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

## Generate A Tile URL

Use `tileUrl()` when an image or another client needs to load one specific tile
directly. Generating the URL does not make an HTTP request.

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

For example, the URL can be used as an image source:

```html
<img src="<?= $url ?>" alt="Precipitation map tile">
```

The URL contains the API key passed to `OpenWeatherMap`. Treat it as a
credential and expose it only where direct client loading is intended.

## Generate A Tile URL Template

Use `tileUrlTemplate()` with an XYZ mapping library. XYZ is a common map-tile
format in which the library replaces `{z}` with the zoom level, `{x}` with the
horizontal tile index, and `{y}` with the vertical tile index as the map moves.

```php
$urlTemplate = $api->maps()->tileUrlTemplate(
    MapLayer::PRECIPITATION,
);
```

The returned URL retains the standard `{z}`, `{x}`, and `{y}` placeholders:

```text
https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=...
```

OpenWeather lists mapping-library integrations in its
[Weather Maps documentation](https://openweathermap.org/api/weathermaps).
The returned format can be passed to
[Leaflet](https://leafletjs.com/reference.html#tilelayer),
[OpenLayers](https://openlayers.org/en/latest/apidoc/module-ol_source_XYZ-XYZ.html),
or a [MapLibre raster source](https://maplibre.org/maplibre-style-spec/sources/).
Like a specific tile URL, the template contains the API key and does not make an
HTTP request when generated.

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

Zoom must be zero or greater. X and Y start at 0, and their highest valid value
is one less than the number of tiles along that axis, as shown in the table.
Mapping libraries normally calculate these indexes from the displayed
geographic area; they should not be replaced directly with a location's
longitude and latitude.

OpenWeather's attribution requirements depend on the applicable license. See
the [official FAQ](https://openweathermap.org/faq) for current guidance.
