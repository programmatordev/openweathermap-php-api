<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithUnitSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\WeatherLocationList;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;
use ProgrammatorDev\OpenWeatherMap\Validator\CoordinateValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\GreaterThanValidatorTrait;

class WeatherEndpoint extends AbstractEndpoint
{
    use WithUnitSystemTrait;
    use WithLanguageTrait;
    use CoordinateValidatorTrait;
    use GreaterThanValidatorTrait;

    private const NUM_RESULTS = 40;

    private string $urlWeather = 'https://api.openweathermap.org/data/2.5/weather';

    private string $urlWeatherForecast = 'https://api.openweathermap.org/data/2.5/forecast';

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getCurrent(float $latitude, float $longitude): WeatherLocation
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlWeather,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherLocation($data);
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getForecast(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): WeatherLocationList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateGreaterThan('numResults', $numResults, 0);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlWeatherForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'cnt' => $numResults,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherLocationList($data);
    }
}