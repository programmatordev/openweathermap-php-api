# Objects

- One Call
- Weather
  - CurrentWeather
  - WeatherList
  - Weather
- Air Pollution
  - CurrentAirPollution
  - AirPollutionList
  - AirPollution
  - AirQuality
- Geocoding
  - ZipCodeLocation
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

### WeatherList

`getNumResults()`: `int`

`getLocation()`: [`Location`](#location)

`getList()`: [`Weather[]`](#weather-1)

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

## Air Pollution

### CurrentAirPollution

`getCoordinate()`: [`Coordinate`](#coordinate)

`getDateTime()`: `\DateTimeImmutable`

`getAirQuality`: [`AirQuality`](#airquality)

`getCarbonMonoxide()`: `float`

`getNitrogenMonoxide()`: `float`

`getNitrogenDioxide()`: `float`

`getOzone()`: `float`

`getSulphurDioxide()`: `float`

`getFineParticulateMatter()`: `float`

`getCoarseParticulateMatter()`: `float`

`getAmmonia()`: `float`

### AirPollutionList

`getCoordinate()`: `float`

`getList()`: [`AirPollution[]`](#airpollution)

### AirPollution

`getDateTime()`: `\DateTimeImmutable`

`getAirQuality`: [`AirQuality`](#airquality)

`getCarbonMonoxide()`: `float`

`getNitrogenMonoxide()`: `float`

`getNitrogenDioxide()`: `float`

`getOzone()`: `float`

`getSulphurDioxide()`: `float`

`getFineParticulateMatter()`: `float`

`getCoarseParticulateMatter()`: `float`

`getAmmonia()`: `float`

### AirQuality

`getIndex()`: `int`

`getQualitativeName()`: `string`

## Geocoding

### ZipCodeLocation

`getZipCode()`: `string`

`getName()`: `string`

`getCoordinate()`: [`Coordinate`](#coordinate)

`getCountryCode()`: `string`

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