# Objects

- [One Call](#one-call)
  - [Alert](#alert)
  - [MinuteForecast](#minuteforecast)
  - [OneCall](#onecall)
  - [Weather](#weather)
  - [WeatherAggregate](#weatheraggregate)
  - [WeatherLocation](#weatherlocation)
- [Weather](#weather-1)
  - [Weather](#weather-2)
  - [WeatherLocation](#weatherlocation-1)
  - [WeatherLocationList](#weatherlocationlist)
- [Air Pollution](#air-pollution)
  - [AirPollution](#airpollution)
  - [AirPollutionLocation](#airpollutionlocation)
  - [AirPollutionLocationList](#airpollutionlocationlist)
  - [AirQuality](#airquality)
- [Geocoding](#geocoding)
  - [ZipCodeLocation](#zipcodelocation)
- [Common](#common)
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

`getMinutelyForecast()`: [`?MinuteForecast[]`](#minuteforecast)

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

### WeatherLocation

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

### WeatherLocation

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

### WeatherLocationList

`getNumResults()`: `int`

`getLocation()`: [`Location`](#location)

`getList()`: [`Weather[]`](#weather-2)

## Air Pollution

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

### AirPollutionLocation

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

### AirPollutionLocationList

`getCoordinate()`: `float`

`getList()`: [`AirPollution[]`](#airpollution)

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