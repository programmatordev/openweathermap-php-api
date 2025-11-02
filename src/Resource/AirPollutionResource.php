<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Method;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\AirPollutionCollection;
use Psr\Http\Client\ClientExceptionInterface;

class AirPollutionResource extends Resource
{
    /**
     * Get access to current air pollution data
     *
     * @throws ClientExceptionInterface
     */
    public function getCurrent(float $latitude, float $longitude): AirPollution
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/data/2.5/air_pollution',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
            ]
        );

        return new AirPollution($data);
    }

    /**
     * Get access to air pollution forecast data per hour
     *
     * @throws ClientExceptionInterface
     */
    public function getForecast(float $latitude, float $longitude): AirPollutionCollection
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/data/2.5/air_pollution/forecast',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
            ]
        );

        return new AirPollutionCollection($data);
    }

    /**
     * Get access to historical air pollution data per hour between two dates
     *
     * @throws ClientExceptionInterface
     */
    public function getHistory(
        float $latitude,
        float $longitude,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): AirPollutionCollection
    {
        $utcTimezone = new \DateTimeZone('UTC');

        $data = $this->api->request(
            method: Method::GET,
            path: '/data/2.5/air_pollution/history',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startDate->setTimezone($utcTimezone)->getTimestamp(),
                'end' => $endDate->setTimezone($utcTimezone)->getTimestamp()
            ]
        );

        return new AirPollutionCollection($data);
    }
}