<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithMeasurementSystemTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\HistoryMoment;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneCall;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidPastDateException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidatePastDateTrait;

class OneCallEndpoint extends AbstractEndpoint
{
    use WithMeasurementSystemTrait;
    use WithLanguageTrait;
    use ValidateCoordinateTrait;
    use ValidatePastDateTrait;

    private string $urlOneCall = 'https://api.openweathermap.org/data/3.0/onecall';

    private string $urlOneCallTimeMachine = 'https://api.openweathermap.org/data/3.0/onecall/timemachine';

    private string $urlOneCallDaySummary = 'https://api.openweathermap.org/data/3.0/onecall/day_summary';

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
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
     * @throws InvalidCoordinateException
     * @throws InvalidNumResultsException
     */
    public function getWeatherByLocationName(string $locationName): OneCall
    {
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getWeather(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude()
        );
    }

    /**
     * @throws Exception
     * @throws InvalidCoordinateException
     * @throws InvalidPastDateException
     */
    public function getHistoryMoment(float $latitude, float $longitude, \DateTimeImmutable $dateTime): HistoryMoment
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validatePastDate('dateTime', $dateTime);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlOneCallTimeMachine,
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
     * @throws InvalidCoordinateException
     * @throws InvalidPastDateException
     * @throws InvalidNumResultsException
     */
    public function getHistoryMomentByLocationName(string $locationName, \DateTimeImmutable $dateTime): HistoryMoment
    {
        $location = $this->api->getGeocoding()->getCoordinatesByLocationName($locationName)[0];

        return $this->getHistoryMoment(
            $location->getCoordinate()->getLatitude(),
            $location->getCoordinate()->getLongitude(),
            $dateTime
        );
    }
}