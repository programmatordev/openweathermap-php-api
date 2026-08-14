# One Call 4.0

One Call 4.0 requires a separate OpenWeather subscription and includes free
daily API calls. Consult OpenWeather's official documentation for the current
allowance, pricing, usage limits, and account configuration before using these
endpoints in production.

## Current

See OpenWeather's
[official One Call API 4.0 documentation](https://openweathermap.org/api/one-call-4#current)
for API details and current subscription terms.

Use `current()` with a latitude and longitude.

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

Measurement getters return nullable values. Companion methods provide the unit
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

The response exposes location metadata and forecast periods. Each period
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

The response exposes location metadata, up to 50 periods, and pagination when
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

The 15-minute, one-hour, and one-day timelines normalize OpenWeather's
fractional precipitation probability to a percentage:

```php
$period->precipitationProbability();          // 91.0
$period->precipitationProbabilityUnit();      // Unit::PERCENT
$period->precipitationProbabilityWithUnit();  // '91 %'
```

## One-hour Timeline

See OpenWeather's
[official One Call 4.0 hourly forecast documentation](https://openweathermap.org/api/one-call-4#hourly)
for API details.

Use `oneHourTimeline()` with a latitude and longitude to retrieve hourly
periods. When `startAt` is omitted, OpenWeather starts the timeline at the
current UTC time.

```php
$timeline = $api->oneCall()->oneHourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Use `startAt` to select a historical or future starting point and `count` to
limit the number of periods returned. `count` must be positive when provided,
and timeline availability depends on OpenWeather.

```php
$timeline = $api->oneCall()->oneHourTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('2 days ago'),
    count: 10,
);
```

The response contains up to 20 periods. Historical and forecast periods expose
their UTC date and time through `dateTime()`.

```php
foreach ($timeline->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
    echo $period->rain()?->lastHour();
    echo $period->snow()?->lastHour();
}
```

## One-day Timeline

See OpenWeather's
[official One Call 4.0 daily forecast documentation](https://openweathermap.org/api/one-call-4#daily)
for API details.

Use `oneDayTimeline()` with a latitude and longitude to retrieve daily periods.
When `startAt` is omitted, OpenWeather starts the timeline at the current UTC
time.

```php
$timeline = $api->oneCall()->oneDayTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Use `startAt` to select a historical or future starting point and `count` to
limit the number of periods returned. Both are optional, and `count` must be
positive when provided.

```php
$timeline = $api->oneCall()->oneDayTimeline(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('2 days from now'),
    count: 5,
);
```

The response contains up to 10 periods. Daily periods provide UTC dates,
astronomy, daily temperatures, weather measurements, conditions, precipitation
probability, rain, snow, and alert references.

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

OpenWeather does not currently define units for the daily scalar rain and snow
values, so these getters return raw nullable floats.

## Timeline Pagination

The 15-minute, one-hour, and one-day timelines provide explicit pagination.

```php
$timeline = $api->oneCall()->oneHourTimeline(
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
is available and otherwise return `null`. Pagination does not iterate
automatically. Every pagination request counts as a separate One Call API call
under your OpenWeather subscription; consult the
[official documentation](https://openweathermap.org/api/one-call-4#pagination)
for current usage and billing terms.

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

Alerts provide sender and event information, validity dates, localized
descriptions, and tags.

`description()` returns the first exact language-code match or `null`.
