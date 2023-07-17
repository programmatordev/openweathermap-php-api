# Entities

- One Call
- Weather
  - CurrentWeather
  - Weather
  - WeatherList
- Air Pollution
- Geocoding
- Common
  - [AtmosphericPressure](#atmosphericpressure)
  - [Coordinate](#coordinate)
  - [Icon](#icon)
  - [Location](#location)
  - [Rain](#rain)
  - [Snow](#snow)
  - [Timezone](#timezone)
  - [Wind](#wind)
  - [WeatherCondition](#weathercondition)

## Weather

### CurrentWeather

`getLocation()`: [`Location`](#location)

`getTemperature()`: `float`

`getTemperatureFeelsLike()`: `float`

`getMinTemperature()`: `float`

`getMaxTemperature()`: `float`

`getHumidity()`: `int`

`getCloudiness()`: `int`

`getVisibility()`: `int`

`getWeatherConditions()`: [`WeatherCondition[]`](#weathercondition)

`getWind()`: [`Wind`](#wind)

`getPrecipitationProbability()`: `?int`

`getRain()`: [`?Rain`](#rain)

`getSnow()`: [`?Snow`](#snow)

`getAtmosphericPressure()`: [`AtmosphericPressure`](#atmosphericpressure)

`getDateTime()`: `\DateTimeImmutable`

### Weather

`getTemperature()`: `float`

`getTemperatureFeelsLike()`: `float`

`getMinTemperature()`: `float`

`getMaxTemperature()`: `float`

`getHumidity()`: `int`

`getCloudiness()`: `int`

`getVisibility()`: `int`

`getWeatherConditions()`: [`WeatherCondition[]`](#weathercondition)

`getWind()`: [`Wind`](#wind)

`getPrecipitationProbability()`: `?int`

`getRain()`: [`?Rain`](#rain)

`getSnow()`: [`?Snow`](#snow)

`getAtmosphericPressure()`: [`AtmosphericPressure`](#atmosphericpressure)

`getDateTime()`: `\DateTimeImmutable`

### WeatherList

`getNumResults()`: `int`

`getLocation()`: [`Location`](#location)

`getList()`: [`Weather[]`](#weather-1)

## Common

### AtmosphericPressure

`getPressure()`: `int`

`getSeaLevelPressure()`: `?int`

`getGroundLevelPressure()`: `?int`

### Coordinate

`getLatitude()`: `float`

`getLongitude()`: `float`

### Icon

`getId()`: `string`

`getImageUrl()`: `string`

### Location

`getId()`: `?int`

`getName()`: `?string`

`getState()`: `?string`

`getCountryCode()`: `?string`

`getLocalNames()`: `?array`

`getPopulation()`: `?int`

`getCoordinate()`: [`Coordinate`](#coordinate)

`getTimezone()`: [`?Timezone`](#timezone)

`getSunriseAt()`: `?\DateTimeImmutable`

`getSunsetAt()`: `?\DateTimeImmutable`

### Rain

`getLastOneHourVolume()`: `?float`

`getLastThreeHoursVolume()`: `?float`

### Snow

`getLastOneHourVolume()`: `?float`

`getLastThreeHoursVolume()`: `?float`

### Timezone

`getIdentifier()`: `?string`

`getOffset()`: `int`

### WeatherCondition

`getId()`: `int`

`getName()`: `string`

`getDescription()`: `string`

`getIcon()`: [`Icon`](#icon)

`getSysName()`: `string`

### Wind

`getSpeed()`: `float`

`getDirection()`: `int`

`getGust()`: `?float`