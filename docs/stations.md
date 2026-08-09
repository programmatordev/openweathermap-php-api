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
