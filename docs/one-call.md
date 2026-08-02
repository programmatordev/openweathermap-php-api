# One Call 4.0

One Call 4.0 requires a separate OpenWeather subscription and includes free
daily API calls. Consult OpenWeather's official documentation for the current
allowance, pricing, usage limits, and account configuration before using these
endpoints in production.

## Current

See OpenWeather's
[official One Call API 4.0 documentation](https://openweathermap.org/api/one-call-4#current)
for the upstream endpoint contract and current subscription terms.

Use `current()` with a latitude and longitude. Both coordinates are validated
before the request is sent.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->oneCall()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Every response property may be absent or explicitly `null`. Coordinates and
timezone metadata describe the requested location, while `dateTime()` and the
astronomical timestamps remain UTC values.

```php
echo $current->coordinates()?->latitude();
echo $current->coordinates()?->longitude();
echo $current->timezone()?->identifier();
echo $current->dateTime()?->format(DATE_ATOM);
echo $current->temperature();

foreach ($current->conditions() as $condition) {
    echo $condition->description();
}
```

Units and language can be overridden for one immutable resource chain:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

$current = $api
    ->oneCall()
    ->withUnits(Units::IMPERIAL)
    ->withLanguage(Language::PORTUGUESE)
    ->current(38.7223, -9.1393);
```

Raw measurement getters return nullable values. Companion methods expose the
effective unit and a locale-independent formatted value. With the default
metric configuration, for example:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

$current->temperature();          // 24.34
$current->temperatureUnit();      // Unit::CELSIUS
$current->temperatureWithUnit();  // '24.34 °C'
```

## Minute Timeline

See OpenWeather's
[official One Call 4.0 minute forecast documentation](https://openweathermap.org/api/one-call-4#min)
for the upstream endpoint contract.

Use `minuteTimeline()` with a latitude and longitude to retrieve up to 60
one-minute forecast periods.

```php
$timeline = $api->oneCall()->minuteTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The response exposes location metadata and a typed collection of periods. Each
period provides its UTC date and time, precipitation, and any referenced alert
IDs.

```php
echo $timeline->coordinates()?->latitude();
echo $timeline->timezone()?->identifier();

foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->precipitation();
    echo $period->precipitationWithUnit();

    foreach ($period->alertIds() as $alertId) {
        echo $alertId;
    }
}
```

One-minute precipitation is always expressed in millimetres per hour, so its
unit is unaffected by the configured weather unit system.

## Fifteen-minute Timeline

See OpenWeather's
[official One Call 4.0 15-minute forecast documentation](https://openweathermap.org/api/one-call-4#15min)
for the upstream endpoint contract.

Use `fifteenMinuteTimeline()` with a latitude and longitude to retrieve the
initial page of 15-minute forecast periods.

```php
$timeline = $api->oneCall()->fifteenMinuteTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    count: 10,
);
```

The optional positive `count` limits the requested page size. When it is
omitted, OpenWeather chooses the page size. OpenWeather also determines the
supported maximum.

The response exposes location metadata, up to 50 typed periods, and passive
pagination URLs when OpenWeather provides them. Pagination URLs are normalized
to HTTPS and stripped of the API key before they are exposed.

```php
echo $timeline->coordinates()?->latitude();
echo $timeline->timezone()?->identifier();
echo $timeline->previousPageUrl();
echo $timeline->nextPageUrl();

foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
}
```

The pagination URL getters return metadata only and do not make another API
request.

## One-hour Timeline

See OpenWeather's
[official One Call 4.0 hourly forecast documentation](https://openweathermap.org/api/one-call-4#hourly)
for the upstream endpoint contract.

Use `oneHourTimeline()` with a latitude and longitude to retrieve the default
hourly timeline.

```php
$timeline = $api->oneCall()->oneHourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Pass an optional `DateTimeInterface` value to select a historical or future
starting point. It is sent to OpenWeather as a Unix timestamp. Actual data
availability is determined by OpenWeather.

```php
$timeline = $api->oneCall()->oneHourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    start: new DateTimeImmutable('2 days ago'),
    count: 10,
);
```

The optional positive `count` limits the requested page size. OpenWeather
determines the supported maximum.

The response contains up to 20 typed periods. Historical and forecast periods
share the same entity and expose their UTC timestamp through `dateTime()`.

```php
foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
    echo $period->rain()?->lastHour();
    echo $period->snow()?->lastHour();
}
```

As with the 15-minute timeline, normalized `previousPageUrl()` and
`nextPageUrl()` values are passive metadata and do not make another request.
