# Supported APIs

- [One Call](#one-call)
  - [getWeather](#getweatherfloat-latitude-float-longitude)
  - [getHistoryMoment](#gethistorymomentfloat-latitude-float-longitude-datetimeinterface-datetime)
  - [getHistoryDaySummary](#gethistorydaysummaryfloat-latitude-float-longitude-datetimeinterface-date)
- [Weather](#weather)
  - [getCurrent](#getcurrentfloat-latitude-float-longitude)
  - [getForecast](#getforecastfloat-latitude-float-longitude-int-numresults--40)
- [Air Pollution](#air-pollution)
  - [getCurrent](#getcurrentfloat-latitude-float-longitude-1)
  - [getForecast](#getforecastfloat-latitude-float-longitude)
  - [getHistory](#gethistoryfloat-latitude-float-longitude-datetimeinterface-startdate-datetimeinterface-enddate)
- [Geocoding](#geocoding)
  - [getByLocationName](#getbylocationnamestring-locationname-int-numresults--5)
  - [getByZipCode](#getbyzipcodestring-zipcode-string-countrycode)
  - [getByCoordinate](#getbycoordinatefloat-latitude-float-longitude-int-numresults--5)

## One Call

### `getWeather(float $latitude, float $longitude)`

Get current and forecast weather data.

Returns a `OneCall` object.

```php
$weather = $openWeatherMap->getOneCall()->getWeather(50, 50);

echo $weather->getCurrent()->getTemperature();
```

### `getHistoryMoment(float $latitude, float $longitude, \DateTimeInterface $dateTime)`

Get weather data from a single moment in the past.

Returns a `HistoryMoment` object.

```php
$weather = $openWeatherMap->getOneCall()->getHistoryMoment(50, 50, new \DateTime('2023-01-01 12:00:00'));

echo $weather->getTemperature();
```

### `getHistoryDaySummary(float $latitude, float $longitude, \DateTimeInterface $date)`

Get aggregated weather data from a single day in the past.

Returns a `HistoryDaySummary` object.

```php
$weather = $openWeatherMap->getOneCall()->getHistoryDaySummary(50, 50, new \DateTime('1985-07-19'));

echo $weather->getTemperature();
```

## Weather

### `getCurrent(float $latitude, float $longitude)`

Get current weather data.

Returns a `CurrentWeather` object.

```php
$weather = $openWeatherMap->getWeather()->getCurrent(50, 50);

echo $weather->getTemperature();
```

### `getForecast(float $latitude, float $longitude, int $numResults = 40)`

Get weather forecast data per 3-hour steps for the next 5 days.

Returns a `WeatherList` object.

```php
// Since it returns 3-hour steps,
// passing 8 as the numResults means it will return results for the next 24 hours
$weatherForecast = $openWeatherMap->getWeather()->getForecast(50, 50, 8);

foreach ($weatherForecast->getList() as $weather) {
    echo $weather->getDateTime()->format('Y-m-d H:i:s');
    echo $weather->getTemperature();
}
```

## Air Pollution

### `getCurrent(float $latitude, float $longitude)`

Get current air pollution data.

Returns a `CurrentAirPollution` object.

```php
$airPollution = $openWeatherMap->getAirPollution()->getCurrent(50, 50);

echo $airPollution->getAirQuality()->getQualitativeName();
echo $airPollution->getCarbonMonoxide();
```

### `getForecast(float $latitude, float $longitude)`

Get air pollution forecast data per 1-hour for the next 24 hours.

Returns a `AirPollutionList` object.

```php
$airPollutionForecast = $openWeatherMap->getAirPollution()->getForecast(50, 50);

foreach ($airPollutionForecast->getList() as $airPollution) {
    echo $airPollution->getDateTime()->format('Y-m-d H:i:s');
    echo $airPollution->getAirQuality()->getQualitativeName();
    echo $airPollution->getCarbonMonoxide();
}
```

### `getHistory(float $latitude, float $longitude, \DateTimeInterface $startDate, \DateTimeInterface $endDate)`

Get air pollution history data between two dates.

Returns a `AirPollutionList` object.

```php
$startDate = new \DateTime('-7 days'); // 7 days ago
$endDate = new \DateTime('-6 days'); // 6 days ago
$airPollutionHistory = $openWeatherMap->getAirPollution()->getHistory(50, 50, $startDate, $endDate);

foreach ($airPollutionHistory->getList() as $airPollution) {
    echo $airPollution->getDateTime()->format('Y-m-d H:i:s');
    echo $airPollution->getAirQuality()->getQualitativeName();
    echo $airPollution->getCarbonMonoxide();
}
```

## Geocoding

### `getByLocationName(string $locationName, int $numResults = 5)`

Get locations data by location name.

Returns an array of `Location` objects.

```php
$locations = $openWeatherMap->getGeocoding()->getByLocationName('lisbon');

foreach ($locations as $location) {
    echo $location->getName();
    echo $location->getCountryCode();
}
```

### `getByZipCode(string $zipCode, string $countryCode)`

Get location data by zip/post code.

Returns a `ZipCodeLocation` object.

```php
$location = $openWeatherMap->getGeocoding()->getByZipCode('1000-001', 'pt');

echo $location->getName();
```

### `getByCoordinate(float $latitude, float $longitude, int $numResults = 5)`

Get locations data by coordinate.

Returns an array of `Location` objects.

```php
$locations = $openWeatherMap->getGeocoding()->getByCoordinate(50, 50);

foreach ($locations as $location) {
    echo $location->getName();
    echo $location->getCountryCode();
}
```
