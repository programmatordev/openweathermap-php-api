# Stations

The Weather Stations API lets you register and manage personal weather stations
associated with your OpenWeather account.

See the
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

The main station properties are required because they describe the station
registered with OpenWeather. Creation and update times are returned as UTC
`DateTimeImmutable` values. The create response may also include `userId()` and
`sourceType()`; other station responses omit them.

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

Create a `Measurement` with the observation time and available readings, then
submit it for a station.

```php
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;

$measurement = new Measurement(
    dateTime: new DateTimeImmutable('now'),
    temperature: 19.5,
    windSpeed: 2.4,
    windDirection: 180,
    pressure: 1012,
    humidity: 68,
);

$api->stations()->submitMeasurement(
    stationId: $station->id(),
    measurement: $measurement,
);
```

Optional METAR cloud and weather observations can be included in a
measurement:

```php
use ProgrammatorDev\OpenWeatherMap\Request\Stations\CloudLayer;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Weather;

$measurement = new Measurement(
    dateTime: new DateTimeImmutable('now'),
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
```

METAR visibility, cloud, and weather values appear to be submission-only in
this API. OpenWeather's aggregate response does not include them, and the
documentation does not provide an endpoint for retrieving the original
measurement payload.

Use `submitMeasurements()` to send several observations in one request.

```php
$api->stations()->submitMeasurements(
    stationId: $station->id(),
    measurements: [
        $firstMeasurement,
        $secondMeasurement,
    ],
);
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

OpenWeather documents `visibilityPrefix` as a compass-direction string, but its
live API rejected a documented string value during verification. Leaving it
`null` avoids this upstream mismatch.

Each `CloudLayer` represents one entry in OpenWeather's `clouds` array. Its
distance, METAR cloud condition, and cumulus type are optional, but at least one
value must be provided.

Each `Weather` represents one entry in the `weather` array. It accepts the
available METAR precipitation, descriptor, intensity, proximity, obscuration,
and other codes. At least one value must be provided, and codes are kept as
strings so additional values accepted by OpenWeather are not restricted.

## Retrieve Measurements

Use `measurements()` to retrieve measurements aggregated by minute, hour, or
day for a station and time range.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\AggregationInterval;

$measurements = $api->stations()->measurements(
    stationId: $station->id(),
    interval: AggregationInterval::HOUR,
    startAt: new DateTimeImmutable('2 days ago'),
    endAt: new DateTimeImmutable('now'),
    limit: 100,
);
```

> **Processing delay:** OpenWeather processes submitted measurements in the
> background. They may take more than 24 hours to appear, and OpenWeather does
> not document how long processing should take.

The method returns an array of `MeasurementAggregate` entities and returns an
empty array when no aggregates are available for the requested interval. Each
entity summarizes readings for one requested minute, hour, or day interval and
identifies the timestamp and station returned by OpenWeather.

```php
foreach ($measurements as $measurement) {
    echo $measurement->interval()?->value;
    echo $measurement->dateTime()?->format(DATE_ATOM);
    echo $measurement->stationId();

    echo $measurement->temperature()?->average(); // 20.5
    echo $measurement->temperature()?->averageWithUnit(); // 20.5 °C
    echo $measurement->humidity()?->averageWithUnit(); // 64 %
    echo $measurement->wind()?->speedWithUnit(); // 3.08 m/s
    echo $measurement->pressure()?->averageWithUnit(); // 1013 hPa
    echo $measurement->precipitation()?->rainWithUnit(); // 0.6 mm
}
```

Temperature and pressure aggregates expose `minimum()`, `maximum()`,
`average()`, and `weight()`. Humidity exposes `average()` and `weight()`. Wind
exposes `direction()` and `speed()`, while precipitation exposes `rain()` and
`snow()`. OpenWeather returns `weight` without documenting its meaning, so
`weight()` exposes the nullable integer unchanged. Measurement properties and
nested structures are nullable because OpenWeather may omit data that was
unavailable for an aggregation interval.

The endpoint returns aggregates rather than the original submitted
measurements. Submitted visibility, cloud layers, METAR weather descriptions,
and other raw fields are not included in the documented aggregate response.
