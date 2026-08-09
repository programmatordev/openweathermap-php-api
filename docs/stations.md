# Weather Stations

The Weather Stations API lets you register and manage personal weather stations
associated with your OpenWeather account.

It is available on OpenWeather's standard free and paid subscriptions. See the
[official Weather Stations documentation](https://openweathermap.org/api/stations)
for API details.

## Create A Station

Use `create()` to register a station with its external ID, name, coordinates,
and altitude.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$station = $api->stations()->create(
    externalId: 'home-station',
    name: 'Home Weather Station',
    latitude: 38.7223,
    longitude: -9.1393,
    altitude: 100,
);
```

The method returns the created `Station`. OpenWeather assigns its internal ID,
rank, user ID, source type, and creation and update times.

## Update A Station

Use `update()` with the internal station ID and the complete editable station
information.

```php
$station = $api->stations()->update(
    id: 'station-id',
    externalId: 'home-station',
    name: 'Home Weather Station',
    latitude: 38.7223,
    longitude: -9.1393,
    altitude: 110,
);
```

OpenWeather does not support partial station updates, so all five station values
are required.

## List Stations

Use `all()` to retrieve every station associated with the authenticated API
key.

```php
$stations = $api->stations()->all();
```

The method returns an array of `Station` entities and returns an empty array
when the account has no registered stations.

```php
foreach ($stations as $station) {
    echo $station->id();
    echo $station->name();
    echo $station->externalId();
    echo $station->latitude();
    echo $station->longitude();
    echo $station->altitude();
    echo $station->rank();
    echo $station->createdAt()->format(DATE_ATOM);
    echo $station->updatedAt()->format(DATE_ATOM);
}
```

Core station properties are required because they describe station metadata
registered with OpenWeather. Creation and update times are returned as UTC
`DateTimeImmutable` values. Registration responses may also populate
`userId()` and `sourceType()`; other station responses omit them.

## Find A Station

Use `find()` with the internal station ID returned by OpenWeather.

```php
$station = $api->stations()->find('station-id');

echo $station->name();
```

## Delete A Station

> **Warning:** Deleting a station also permanently deletes its associated
> measurements.

Use `delete()` with the internal station ID returned by OpenWeather.

```php
$api->stations()->delete('station-id');
```

## Submit Measurements

Create a `Measurement` with the station ID, observation time, and available
readings, then submit it to OpenWeather.

```php
use ProgrammatorDev\OpenWeatherMap\Request\Stations\CloudLayer;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Weather;

$measurement = new Measurement(
    stationId: $station->id(),
    dateTime: new DateTimeImmutable('now'),
    temperature: 19.5,
    windSpeed: 2.4,
    windDirection: 180,
    pressure: 1012,
    humidity: 68,
    clouds: [
        new CloudLayer(
            distance: 1200,
            condition: 'BKN',
            cumulus: 'CB',
        ),
    ],
    weather: [
        new Weather(
            precipitation: 'RA',
            intensity: '-',
        ),
    ],
);

$api->stations()->submitMeasurement($measurement);
```

Use `submitMeasurements()` to send several observations in one request.

```php
$api->stations()->submitMeasurements([
    $firstMeasurement,
    $secondMeasurement,
]);
```

Measurement units are fixed by the Weather Stations API: Celsius for
temperatures, metres per second for wind, degrees for wind direction,
hectopascals for pressure, percent for humidity, millimetres for rain and snow,
kilometres for visibility, and metres for cloud-layer distance. The API-wide
units configuration does not alter submitted values.

Visibility prefixes, cloud conditions, cumulus types, and weather values use
standard METAR codes. See the
[NOAA METAR reference](https://aviationweather.gov/help/data/#metar) for their
meanings.

Each `CloudLayer` represents one entry in OpenWeather's `clouds` array. Its
distance, METAR cloud condition, and cumulus type are optional, but at least one
value must be provided.

Each `Weather` represents one entry in the `weather` array. It accepts the
available METAR precipitation, descriptor, intensity, proximity, obscuration,
and other codes. At least one value must be provided, and codes are kept as
strings so additional values accepted by OpenWeather are not restricted.
