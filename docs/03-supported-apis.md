# Supported APIs

- [One Call](#one-call)
  - [getWeather](#getweatherfloat-latitude-float-longitude)
  - [getHistoryMoment](#gethistorymomentfloat-latitude-float-longitude-datetimeinterface-datetime)
  - [getHistoryDaySummary](#gethistorydaysummaryfloat-latitude-float-longitude-datetimeinterface-date)
- Weather
  - getCurrent
  - getForecast
- Air Pollution
  - getCurrent
  - getForecast
  - getHistory
- Geocoding
  - getCoordinatesByLocationName
  - getCoordinatesByZipCode
  - getLocationNameByCoordinates

## One Call

### `getWeather(float $latitude, float $longitude)`

Get current and forecast weather data.

Returns a `OneCall` object.

Example:

```php
$weather = $openWeatherMap->getOneCall()->getWeather(50, 50);

echo $weather->getCurrent()->getTemperature();
```

### `getHistoryMoment(float $latitude, float $longitude, \DateTimeInterface $dateTime)`

Get weather data from a single moment in the past.

Returns a `HistoryMoment` object.

Example:

```php
$weather = $openWeatherMap->getOneCall()->getHistoryMoment(50, 50, new \DateTime('2023-01-01 12:00:00'));

echo $weather->getTemperature();
```

### `getHistoryDaySummary(float $latitude, float $longitude, \DateTimeInterface $date)`

Get aggregated weather data from a single day in the past.

Return a `HistoryDaySummary` object.

Example:

```php
$weather = $openWeatherMap->getOneCall()->getHistoryDaySummary(50, 50, new \DateTime('1985-07-19'));

echo $weather->getTemperature();
```