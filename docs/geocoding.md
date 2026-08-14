# Geocoding

The Geocoding API is available on OpenWeather's standard free and paid
subscriptions. See the
[official Geocoding API documentation](https://openweathermap.org/api/geocoding-api)
for API details.

## Lookup By Name

Use `byName()` with a city name, optionally followed by a state code and
two-letter ISO 3166 country code: `city`, `city,country`, or
`city,state,country`. The state code is intended for US locations. The optional
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
    echo $location->latitude();
    echo $location->longitude();
    echo $location->countryCode();
    echo $location->state();
}
```

`localNames()` returns all available localized names as an associative array.
The available language codes depend on the returned location.

## Lookup By Postal Code

Use `byPostalCode()` with a postal code and a two-letter ISO 3166 country code.
Country codes are case-insensitive.

```php
$location = $api->geocoding()->byPostalCode(
    postalCode: '1000-001',
    countryCode: 'PT',
);

echo $location->postalCode();
echo $location->name();
echo $location->latitude();
echo $location->longitude();
echo $location->countryCode();
```

The method returns a `PostalLocation`. Every response property may be absent or
`null`.

## Lookup By Coordinates

Use `byCoordinates()` for reverse geocoding. The optional result limit must be
at least one; omit it to use the API default.

```php
$locations = $api->geocoding()->byCoordinates(
    latitude: 40.7128,
    longitude: -74.006,
    limit: 5,
);
```

The method returns an array of `Location` entities.
