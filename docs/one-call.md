# One Call 4.0

OpenWeather manages access and usage terms for One Call 4.0. Consult the
[official One Call documentation](https://openweathermap.org/api/one-call-4)
for current requirements before using these endpoints.

## Current

See OpenWeather's
[official One Call API 4.0 documentation](https://openweathermap.org/api/one-call-4#current)
for API details.

Use `current()` with a latitude and longitude.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->oneCall()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Every response property may be absent or `null`. Coordinates and timezone
details describe the requested location. `dateTime()`, sunrise, and sunset use
UTC.

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

Configure units and language for a request:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

$current = $api
    ->oneCall()
    ->withUnits(Units::IMPERIAL)
    ->withLanguage(Language::PORTUGUESE)
    ->current(38.7223, -9.1393);
```

Measurement getters return nullable values. Related methods provide the unit
and a formatted value. With the default metric configuration, for example:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

$current->temperature();          // 24.34
$current->temperatureUnit();      // Unit::CELSIUS
$current->temperatureWithUnit();  // '24.34 °C'
```

## Minute Timeline

See OpenWeather's
[official One Call 4.0 minute forecast documentation](https://openweathermap.org/api/one-call-4#min)
for API details.

Use `minuteTimeline()` with a latitude and longitude to retrieve up to 60
one-minute forecast periods.

```php
$timeline = $api->oneCall()->minuteTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The response contains location details and forecast periods. Each period
provides its UTC date and time, precipitation, and any referenced alert IDs.

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
for API details.

Use `fifteenMinuteTimeline()` with a latitude and longitude to retrieve
15-minute forecast periods. When `startAt` is omitted, OpenWeather starts the
timeline at the current UTC time.

```php
$timeline = $api->oneCall()->fifteenMinuteTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('1 day from now'),
    count: 10,
);
```

Use `startAt` to select a future starting point and `count` to limit the number
of periods returned. Both are optional, and `count` must be positive when
provided.

The response contains location details, up to 50 periods, and pagination when
OpenWeather provides it.

```php
echo $timeline->coordinates()?->latitude();
echo $timeline->timezone()?->identifier();

foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
}
```

Precipitation probability follows the same getter pattern as other
measurements:

```php
$period->precipitationProbability();          // 91.0
$period->precipitationProbabilityUnit();      // Unit::PERCENT
$period->precipitationProbabilityWithUnit();  // '91 %'
```

## Hour Timeline

See OpenWeather's
[official One Call 4.0 hourly forecast documentation](https://openweathermap.org/api/one-call-4#hourly)
for API details.

Use `hourTimeline()` with a latitude and longitude to retrieve hourly
periods. When `startAt` is omitted, OpenWeather starts the timeline at the
current UTC time.

```php
$timeline = $api->oneCall()->hourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Use `startAt` to select a historical or future starting point and `count` to
limit the number of periods returned. `count` must be positive when provided,
and timeline availability depends on OpenWeather.

```php
$timeline = $api->oneCall()->hourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('2 days ago'),
    count: 10,
);
```

The response contains up to 20 periods. `dateTime()` returns each period's UTC
date and time.

```php
foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
    echo $period->rain()?->lastHour();
    echo $period->snow()?->lastHour();
}
```

## Day Timeline

See OpenWeather's
[official One Call 4.0 daily forecast documentation](https://openweathermap.org/api/one-call-4#daily)
for API details.

Use `dayTimeline()` with a latitude and longitude to retrieve daily periods.
When `startAt` is omitted, OpenWeather starts the timeline at the current UTC
time.

```php
$timeline = $api->oneCall()->dayTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Use `startAt` to select a historical or future starting point and `count` to
limit the number of periods returned. Both are optional, and `count` must be
positive when provided.

```php
$timeline = $api->oneCall()->dayTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('2 days from now'),
    count: 5,
);
```

The response contains up to 10 periods. Daily periods provide UTC dates, sun
and moon times, daily temperatures, weather measurements, conditions,
precipitation probability, rain, snow, and alert references.

```php
foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->sunriseAt()?->format(DATE_ATOM);
    echo $period->moonPhase();
    echo $period->temperature()?->day();
    echo $period->temperature()?->minimum();
    echo $period->temperature()?->maximum();
    echo $period->rain();
    echo $period->snow();
}
```

OpenWeather does not define units for the daily `rain` and `snow` values, so
these getters return nullable floats without conversion.

## Timeline Pagination

The 15-minute, one-hour, and one-day timelines support pagination.

```php
$timeline = $api->oneCall()->hourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);

$pagination = $timeline->pagination();

if ($pagination->hasPreviousPage()) {
    $previousTimeline = $pagination->previousPage();
}

if ($pagination->hasNextPage()) {
    $nextTimeline = $pagination->nextPage();
}

echo $pagination->previousPageUrl();
echo $pagination->nextPageUrl();
```

The availability checks and URL getters do not make another API request.
`previousPage()` and `nextPage()` request the corresponding page when its URL
is available and otherwise return `null`. The library does not fetch every page
automatically. Each page navigation sends a separate API request.

## Alert

See OpenWeather's
[official One Call 4.0 weather alert documentation](https://openweathermap.org/api/one-call-4#alerts)
for API details.

Current weather and timeline periods may provide alert IDs. Use `alert()` with
one of those IDs to retrieve the corresponding alert.

```php
$current = $api->oneCall()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);

foreach ($current->alertIds() as $id) {
    $alert = $api->oneCall()->alert($id);

    echo $alert->id();
    echo $alert->senderName();
    echo $alert->event();
    echo $alert->startsAt()?->format(DATE_ATOM);
    echo $alert->endsAt()?->format(DATE_ATOM);
    echo $alert->description('en-US');

    foreach ($alert->descriptions() as $description) {
        echo $description->languageCode();
        echo $description->text();
    }

    foreach ($alert->tags() as $tag) {
        echo $tag;
    }
}
```

Alerts provide sender and event information, start and end times, localized
descriptions, and tags.

`description()` returns the first exact language-code match or `null`.
