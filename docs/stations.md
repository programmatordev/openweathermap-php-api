# Weather Stations

Weather Stations API 3.0 manages personal weather stations associated with an
OpenWeather account and is available on OpenWeather's standard free and paid
subscriptions. See the
[official Weather Stations documentation](https://openweathermap.org/api/stations)
for API details.

## List Stations

Use `all()` to retrieve every station associated with the authenticated API
key.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$stations = $api->stations()->all();
```

The method returns an array of `Station` entities and returns an empty array
when the account has no registered stations.

```php
foreach ($stations as $station) {
    echo $station->id();
    echo $station->name();
    echo $station->externalId();
    echo $station->coordinates()?->latitude();
    echo $station->coordinates()?->longitude();
    echo $station->altitude();
    echo $station->rank();
    echo $station->createdAt()?->format(DATE_ATOM);
    echo $station->updatedAt()?->format(DATE_ATOM);
}
```

Every response property may be absent or `null`. Creation and update times are
returned as UTC `DateTimeImmutable` values. Registration responses may also
populate `userId()` and `sourceType()`; other station responses omit them.

## Find A Station

Use `find()` with the internal station ID returned by OpenWeather.

```php
$station = $api->stations()->find('station-id');

echo $station->name();
```
