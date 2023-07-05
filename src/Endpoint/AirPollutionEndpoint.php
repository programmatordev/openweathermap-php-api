<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\CurrentAirPollution;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidDateRangeException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidPastDateException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateDateRangeTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidatePastDateTrait;

class AirPollutionEndpoint extends AbstractEndpoint
{
    use ValidateCoordinateTrait;
    use ValidateDateRangeTrait;
    use ValidatePastDateTrait;

    private string $urlAirPollution = 'https://api.openweathermap.org/data/2.5/air_pollution';

    private string $urlAirPollutionForecast = 'https://api.openweathermap.org/data/2.5/air_pollution/forecast';

    private string $urlAirPollutionHistory = 'https://api.openweathermap.org/data/2.5/air_pollution/history';

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getCurrent(float $latitude, float $longitude): CurrentAirPollution
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollution,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new CurrentAirPollution($data);
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getForecast(float $latitude, float $longitude): AirPollutionList
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionForecast,
            query: [
                'lat' => $latitude,
                'lon' => $longitude
            ]
        );

        return new AirPollutionList($data);
    }

    /**
     * @throws InvalidDateRangeException
     * @throws InvalidPastDateException
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ): AirPollutionList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validatePastDate('startDate', $startDate);
        $this->validatePastDate('endDate', $endDate);
        $this->validateDateRange($startDate, $endDate);

        $timezoneUtc = new \DateTimeZone('UTC');

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlAirPollutionHistory,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startDate->setTimezone($timezoneUtc)->getTimestamp(),
                'end' => $endDate->setTimezone($timezoneUtc)->getTimestamp()
            ]
        );

        return new AirPollutionList($data);
    }
}