# Geocoding

The Geocoding API is available on OpenWeather's standard free and paid
subscriptions. See the
[official Geocoding API documentation](https://openweathermap.org/api/geocoding-api)
for the upstream endpoint contract.

## Lookup By Name

Use `byName()` with OpenWeather's comma-separated location query. The optional
result limit must be between one and five; omit it to use the API default.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$locations = $api->geocoding()->byName('Springfield,US', limit: 5);
```

The method returns an array of `Location` entities and returns an empty array
when no locations match. Every response property may be absent or `null`.

```php
foreach ($locations as $location) {
    echo $location->name();
    echo $location->localName('en');
    echo $location->coordinates()?->latitude();
    echo $location->coordinates()?->longitude();
    echo $location->countryCode();
    echo $location->state();
}
```

`localNames()` returns all available localized names as an associative array.
The available language codes depend on the returned location.
