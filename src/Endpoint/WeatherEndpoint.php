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
use ProgrammatorDev\OpenWeatherMap\Validator\CoordinateValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\GreaterThanValidatorTrait;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

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
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

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
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::greaterThan(0)->assert($numResults, 'numResults');

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