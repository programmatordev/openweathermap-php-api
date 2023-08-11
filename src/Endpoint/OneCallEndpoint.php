<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithUnitSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherAggregate;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\WeatherLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Validator\CoordinateValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\LessThanValidatorTrait;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class OneCallEndpoint extends AbstractEndpoint
{
    use WithUnitSystemTrait;
    use WithLanguageTrait;
    use CoordinateValidatorTrait;
    use LessThanValidatorTrait;

    private string $urlOneCall = 'https://api.openweathermap.org/data/3.0/onecall';

    private string $urlOneCallHistoryMoment = 'https://api.openweathermap.org/data/3.0/onecall/timemachine';

    private string $urlOneCallHistoryAggregate = 'https://api.openweathermap.org/data/3.0/onecall/day_summary';

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getWeather(float $latitude, float $longitude): OneCall
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCall,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new OneCall($data);
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
    public function getHistoryMoment(float $latitude, float $longitude, \DateTimeInterface $dateTime): WeatherLocation
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::lessThan(new \DateTime('now'))->assert($dateTime, 'dateTime');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCallHistoryMoment,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'dt' => $dateTime->setTimezone(new \DateTimeZone('UTC'))->getTimestamp(),
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
    public function getHistoryAggregate(float $latitude, float $longitude, \DateTimeInterface $date): WeatherAggregate
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::lessThan(new \DateTime('now'))->assert($date, 'date');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCallHistoryAggregate,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'date' => $date->format('Y-m-d'),
                'units' => $this->getUnitSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new WeatherAggregate($data);
    }
}