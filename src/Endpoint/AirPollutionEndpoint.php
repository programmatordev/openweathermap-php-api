<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionList;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\CurrentAirPollution;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Validator\CoordinateValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\LessThanValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\RangeValidatorTrait;
use Psr\Cache\InvalidArgumentException;

class AirPollutionEndpoint extends AbstractEndpoint
{
    use CoordinateValidatorTrait;
    use LessThanValidatorTrait;
    use RangeValidatorTrait;

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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws InvalidArgumentException
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ): AirPollutionList
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateLessThan('startDate', $startDate, new \DateTimeImmutable('now'));
        $this->validateLessThan('endDate', $endDate, new \DateTimeImmutable('now'));
        $this->validateRange('startDate', 'endDate', $startDate, $endDate);

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