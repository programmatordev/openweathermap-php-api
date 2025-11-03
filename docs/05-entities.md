# Entities

- [One Call](#one-call)
  - [Weather](#weather)
  - [WeatherMoment](#weathermoment)
  - [WeatherSummary](#weathersummary)
  - [WeatherData](#weatherdata)
  - [MinuteData](#minutedata)
  - [HourData](#hourdata)
  - [DayData](#daydata)
  - [Alert](#alert)
  - [MoonPhase](#moonphase)
  - [Temperature](#temperature)
- [Weather](#weather-1)
  - [Weather](#weather-2)
  - [WeatherCollection](#weathercollection)
  - [WeatherData](#weatherdata)
- [Air Pollution](#air-pollution)
  - [AirPollution](#airpollution)
  - [AirPollutionCollection](#airpollutioncollection)
  - [AirPollutionData](#airpollutiondata)
  - [AirQuality](#airquality)
- [Geocoding](#geocoding)
  - [ZipLocation](#ziplocation)
- [Common](#common)
  - [Coordinate](#coordinate)
  - [Condition](#condition)
  - [Icon](#icon)
  - [Location](#location)
  - [Timezone](#timezone)
  - [Wind](#wind)

## One Call

### Weather

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getTimezone()`: [`Timezone`](#timezone)
- `getCurrent()`: [`WeatherData`](#weatherdata)
- `getMinutelyForecast()`: [`?MinuteData[]`](#minutedata)
- `getHourlyForecast()`: [`HourData[]`](#hourdata)
- `getDailyForecast()`: [`DayData[]`](#daydata)
- `getAlerts()`: [`?Alert[]`](#alert)

### WeatherMoment

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getTimezone()`: [`Timezone`](#timezone)
- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: `float`
- `getTemperatureFeelsLike()`: `float`
- `getAtmosphericPressure()`: `int`
- `getHumidity()`: `int`
- `getDewPoint()`: `float`
- `getUltraVioletIndex()`: `?float`
- `getCloudiness()`: `int`
- `getVisibility()`: `?int`
- `getWind()`: [`Wind`](#wind)
- `getConditions()`: [`Condition[]`](#condition)
- `getSummary()`: `?string`
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`
- `getMoonPhase()`: [`?MoonPhase`](#moonphase)
- `getSunriseAt()`: `?\DateTimeImmutable`
- `getSunsetAt()`: `?\DateTimeImmutable`
- `getMoonriseAt()`: `?\DateTimeImmutable`
- `getMoonsetAt()`: `?\DateTimeImmutable`

### WeatherSummary

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getTimezone()`: [`Timezone`](#timezone)
- `getDateTime()`: `\DateTimeImmutable`
- `getCloudiness()`: `int`
- `getHumidity()`: `int`
- `getPrecipitation()`: `float`
- `getTemperature()`: [`Temperature`](#temperature)
- `getAtmosphericPressure()`: `int`
- `getWind()`: [`Wind`](#wind)

### WeatherOverview

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getTimezone()`: [`Timezone`](#timezone)
- `getDateTime()`: `\DateTimeImmutable`
- `getOverview()`: `string`

### WeatherData

- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: `float`
- `getTemperatureFeelsLike()`: `float`
- `getAtmosphericPressure()`: `int`
- `getVisibility()`: `?int`
- `getHumidity()`: `int`
- `getDewPoint()`: `float`
- `getUltraVioletIndex()`: `?float`
- `getCloudiness()`: `int`
- `getWind()`: [`Wind`](#wind)
- `getConditions()`: [`Condition[]`](#condition)
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`
- `getSunriseAt()`: `?\DateTimeImmutable`
- `getSunsetAt()`: `?\DateTimeImmutable`

### MinuteData

- `getDateTime()`: `\DateTimeImmutable`
- `getPrecipitation()`: `float`

### HourData

- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: `float`
- `getTemperatureFeelsLike()`: `float`
- `getVisibility()`: `int`
- `getPrecipitationProbability()`: `int`
- `getAtmosphericPressure()`: `int`
- `getHumidity()`: `int`
- `getDewPoint()`: `float`
- `getUltraVioletIndex()`: `?float`
- `getCloudiness()`: `int`
- `getWind()`: [`Wind`](#wind)
- `getConditions()`: [`Condition[]`](#condition)
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`

### DayData

- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: [`Temperature`](#temperature)
- `getTemperatureFeelsLike()`: [`Temperature`](#temperature)
- `getPrecipitationProbability()`: `int`
- `getAtmosphericPressure()`: `int`
- `getHumidity()`: `int`
- `getDewPoint()`: `float`
- `getUltraVioletIndex()`: `?float`
- `getCloudiness()`: `int`
- `getWind()`: [`Wind`](#wind)
- `getConditions()`: [`Condition[]`](#condition)
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`
- `getSummary()`: `string`
- `getMoonPhase()`: [`MoonPhase`](#moonphase)
- `getSunriseAt()`: `\DateTimeImmutable`
- `getSunsetAt()`: `\DateTimeImmutable`
- `getMoonriseAt()`: `\DateTimeImmutable`
- `getMoonsetAt()`: `\DateTimeImmutable`

### Alert

- `getSenderName()`: `string`
- `getEventName()`: `string`
- `getStartsAt()`: `\DateTimeImmutable`
- `getEndsAt()`: `\DateTimeImmutable`
- `getDescription()`: `string`
- `getTags()`: `array`

### MoonPhase

- `getValue()`: `float`
- `getName()`: `string`
- `getSystemName()`: `string`

### Temperature

- `getMorning()`: `float`
- `getDay()`: `float`
- `getEvening()`: `float`
- `getNight()`: `float`
- `getMin()`: `?float`
- `getMax()`: `?float`

## Weather

### Weather

- `getLocation()`: [`Location`](#location)
- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: `float`
- `getTemperatureFeelsLike()`: `float`
- `getMinTemperature()`: `float`
- `getMaxTemperature()`: `float`
- `getHumidity()`: `int`
- `getCloudiness()`: `int`
- `getVisibility()`: `int`
- `getAtmosphericPressure()`: `int`
- `getConditions()`: [`Condition[]`](#condition)
- `getWind()`: [`Wind`](#wind)
- `getPrecipitationProbability()`: `?int`
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`

### WeatherCollection

- `getNumResults()`: `int`
- `getLocation()`: [`Location`](#location)
- `getData()`: [`WeatherData[]`](#weatherdata-1)

### WeatherData

- `getDateTime()`: `\DateTimeImmutable`
- `getTemperature()`: `float`
- `getTemperatureFeelsLike()`: `float`
- `getMinTemperature()`: `float`
- `getMaxTemperature()`: `float`
- `getHumidity()`: `int`
- `getCloudiness()`: `int`
- `getVisibility()`: `int`
- `getAtmosphericPressure()`: `int`
- `getConditions()`: [`Condition[]`](#condition)
- `getWind()`: [`Wind`](#wind)
- `getPrecipitationProbability()`: `?int`
- `getRainVolume()`: `?float`
- `getSnowVolume()`: `?float`

## Air Pollution

### AirPollution

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getDateTime()`: `\DateTimeImmutable`
- `getAirQuality`: [`AirQuality`](#airquality)
- `getCarbonMonoxide()`: `float`
- `getNitrogenMonoxide()`: `float`
- `getNitrogenDioxide()`: `float`
- `getOzone()`: `float`
- `getSulphurDioxide()`: `float`
- `getFineParticulateMatter()`: `float`
- `getCoarseParticulateMatter()`: `float`
- `getAmmonia()`: `float`

### AirPollutionCollection

- `getNumResults()`: `int`
- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getData()`: [`AirPollutionData[]`](#airpollutiondata)

### AirPollutionData

- `getDateTime()`: `\DateTimeImmutable`
- `getAirQuality`: [`AirQuality`](#airquality)
- `getCarbonMonoxide()`: `float`
- `getNitrogenMonoxide()`: `float`
- `getNitrogenDioxide()`: `float`
- `getOzone()`: `float`
- `getSulphurDioxide()`: `float`
- `getFineParticulateMatter()`: `float`
- `getCoarseParticulateMatter()`: `float`
- `getAmmonia()`: `float`

### AirQuality

- `getIndex()`: `int`
- `getQualitativeName()`: `string`

## Geocoding

### ZipLocation

- `getZipCode()`: `string`
- `getName()`: `string`
- `getCountryCode()`: `string`
- `getCoordinate()`: [`Coordinate`](#coordinate)

## Common

### Coordinate

- `getLatitude()`: `float`
- `getLongitude()`: `float`

### Condition

- `getId()`: `int`
- `getName()`: `string`
- `getDescription()`: `string`
- `getIcon()`: [`Icon`](#icon)
- `getSystemName()`: `string`

### Icon

- `getId()`: `string`
- `getUrl()`: `string`

### Location

- `getCoordinate()`: [`Coordinate`](#coordinate)
- `getId()`: `?int`
- `getName()`: `?string`
- `getState()`: `?string`
- `getCountryCode()`: `?string`
- `getLocalNames()`: `?array`
- `getLocalName(string $countryCode)`: `?string`
- `getPopulation()`: `?int`
- `getTimezone()`: [`?Timezone`](#timezone)
- `getSunriseAt()`: `?\DateTimeImmutable`
- `getSunsetAt()`: `?\DateTimeImmutable`

### Timezone

- `getOffset()`: `int`
- `getIdentifier()`: `?string`

### Wind

- `getSpeed()`: `float`
- `getDirection()`: `int`
- `getGust()`: `?float`