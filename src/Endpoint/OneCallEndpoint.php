<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithMeasurementSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\HistoryDaySummary;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\HistoryMoment;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateLessThanTrait;

class OneCallEndpoint extends AbstractEndpoint
{
    use WithMeasurementSystemTrait;
    use WithLanguageTrait;
    use ValidateCoordinateTrait;
    use ValidateLessThanTrait;

    private string $urlOneCall = 'https://api.openweathermap.org/data/3.0/onecall';

    private string $urlOneCallHistoryMoment = 'https://api.openweathermap.org/data/3.0/onecall/timemachine';

    private string $urlOneCallHistoryDaySummary = 'https://api.openweathermap.org/data/3.0/onecall/day_summary';

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getWeather(float $latitude, float $longitude): OneCall
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCall,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->getMeasurementSystem(),
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
     */
    public function getHistoryMoment(float $latitude, float $longitude, \DateTimeImmutable $dateTime): HistoryMoment
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateLessThan('dateTime', $dateTime, new \DateTimeImmutable('now'));

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCallHistoryMoment,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'dt' => $dateTime->setTimezone(new \DateTimeZone('UTC'))->getTimestamp(),
                'units' => $this->getMeasurementSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new HistoryMoment($data);
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getHistoryDaySummary(float $latitude, float $longitude, \DateTimeImmutable $dateTime): HistoryDaySummary
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateLessThan('dateTime', $dateTime, new \DateTimeImmutable('now'));

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCallHistoryDaySummary,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'date' => $dateTime->format('Y-m-d'),
                'units' => $this->getMeasurementSystem(),
                'lang' => $this->getLanguage()
            ]
        );

        return new HistoryDaySummary($data);
    }
}