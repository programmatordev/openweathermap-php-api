# Objects

- One Call
  - Alert
  - MinuteForecast
  - OneCall
  - Weather
  - WeatherAggregate
  - WeatherMoment
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
  - [MoonPhase](#moonphase)
  - [Rain](#rain)
  - [Snow](#snow)
  - [Temperature](#temperature)
  - [Timezone](#timezone)
  - [Wind](#wind)
  - [WeatherCondition](#weathercondition)

## One Call

### Alert

`getSenderName()`: `string`

`getEventName()`: `string`

`getStartsAt()`: `\DateTimeImmutable`

`getEndsAt()`: `\DateTimeImmutable`

`getDescription()`: `string`

`getTags()`: `array`

### MinuteForecast

`getDateTime()`: `\DateTimeImmutable`

`getPrecipitation()`: `float`

### OneCall

`getCoordinate()`: [`Coordinate`](#coordinate)

`getTimezone()`: [`Timezone`](#timezone)

`getCurrent()`: [`Weather`](#weather)

`getMinutelyForecast()`: [`?MinutelyForecast[]`](#minuteforecast)

`getHourlyForecast()`: [`Weather[]`](#weather)

`getDailyForecast()`: [`Weather[]`](#weather)

`getAlerts()`: [`?Alert[]`](#alert)

### Weather

`getDateTime()`: `\DateTimeImmutable`

`getSunriseAt()`: `?\DateTimeImmutable`

`getSunsetAt()`: `?\DateTimeImmutable`

`getMoonriseAt()`: `?\DateTimeImmutable`

`getMoonsetAt()`: `?\DateTimeImmutable`

`getMoonPhase()`: [`?MoonPhase`](#moonphase)

`getTemperature()`: `float`|[`Temperature`](#temperature)

`getTemperatureFeelsLike()`: `float`|[`Temperature`](#temperature)

`getDescription()`: `?string`

`getAtmosphericPressure()`: `int`

`getHumidity()`: `int`

`getDewPoint()`: `?float`

`getUltraVioletIndex()`: `?float`

`getCloudiness()`: `int`

`getVisibility()`: `?int`

`getWind()`: [`Wind`](#wind)

`getPrecipitationProbability()`: `?int`

`getRain()`: `null`|`float`|[`Rain`](#rain)

`getSnow()`: `null`|`float`|[`Snow`](#snow)

`getWeatherConditions()`: [`WeatherCondition[]`](#weathercondition)

### WeatherAggregate

`getCoordinate()`: [`Coordinate`](#coordinate)

`getTimezone()`: [`Timezone`](#timezone)

`getDateTime()`: `\DateTimeImmutable`

`getCloudiness()`: `int`

`getHumidity()`: `int`

`getPrecipitation()`: `float`

`getTemperature()`: [`Temperature`](#temperature)

`getAtmosphericPressure()`: `int`

`getWind()`: [`Wind`](#wind)

### WeatherMoment

`getCoordinate()`: [`Coordinate`](#coordinate)

`getTimezone()`: [`Timezone`](#timezone)

`getDateTime()`: `\DateTimeImmutable`

`getSunriseAt()`: `?\DateTimeImmutable`

`getSunsetAt()`: `?\DateTimeImmutable`

`getMoonriseAt()`: `?\DateTimeImmutable`

`getMoonsetAt()`: `?\DateTimeImmutable`

`getMoonPhase()`: [`?MoonPhase`](#moonphase)

`getTemperature()`: `float`|[`Temperature`](#temperature)

`getTemperatureFeelsLike()`: `float`|[`Temperature`](#temperature)

`getDescription()`: `?string`

`getAtmosphericPressure()`: `int`

`getHumidity()`: `int`

`getDewPoint()`: `?float`

`getUltraVioletIndex()`: `?float`

`getCloudiness()`: `int`

`getVisibility()`: `?int`

`getWind()`: [`Wind`](#wind)

`getPrecipitationProbability()`: `?int`

`getRain()`: `null`|`float`|[`Rain`](#rain)

`getSnow()`: `null`|`float`|[`Snow`](#snow)

`getWeatherConditions()`: [`WeatherCondition[]`](#weathercondition)

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

### MoonPhase

`getValue()`: `float`

`getName()`: `string`

`getSysName()`: `string`

### Rain

`getLastOneHourVolume()`: `?float`

`getLastThreeHoursVolume()`: `?float`

### Snow

`getLastOneHourVolume()`: `?float`

`getLastThreeHoursVolume()`: `?float`

### Temperature

`getMorning()`: `float`

`getDay()`: `float`

`getEvening()`: `float`

`getNight()`: `float`

`getMin()`: `?float`

`getMax()`: `?float`

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