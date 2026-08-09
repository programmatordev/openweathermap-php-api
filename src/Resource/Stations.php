<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Stations extends Resource
{
    public function create(
        string $externalId,
        string $name,
        float $latitude,
        float $longitude,
        float $altitude,
    ): Station {
        // https://openweathermap.org/api/stations
        /** @var Station $station */
        $station = $this
            ->endpoint()
            ->json(self::stationPayload(
                $externalId,
                $name,
                $latitude,
                $longitude,
                $altitude,
            ))
            ->post('/data/3.0/stations')
            ->entity(Station::class);

        return $station;
    }

    public function update(
        string $id,
        string $externalId,
        string $name,
        float $latitude,
        float $longitude,
        float $altitude,
    ): Station {
        $id = Assert::notBlank($id, 'station ID');

        // https://openweathermap.org/api/stations
        /** @var Station $station */
        $station = $this
            ->endpoint()
            ->json(self::stationPayload(
                $externalId,
                $name,
                $latitude,
                $longitude,
                $altitude,
            ))
            ->put('/data/3.0/stations/{id}', [
                'id' => $id,
            ])
            ->entity(Station::class);

        return $station;
    }

    /**
     * @return list<Station>
     */
    public function all(): array
    {
        // https://openweathermap.org/api/stations
        return $this
            ->endpoint()
            ->get('/data/3.0/stations')
            ->collection(Station::class);
    }

    public function find(string $id): Station
    {
        $id = Assert::notBlank($id, 'station ID');

        // https://openweathermap.org/api/stations
        /** @var Station $station */
        $station = $this
            ->endpoint()
            ->get('/data/3.0/stations/{id}', [
                'id' => $id,
            ])
            ->entity(Station::class);

        return $station;
    }

    public function delete(string $id): void
    {
        $id = Assert::notBlank($id, 'station ID');

        // https://openweathermap.org/api/stations
        $this
            ->endpoint()
            ->delete('/data/3.0/stations/{id}', [
                'id' => $id,
            ]);
    }

    public function submitMeasurement(
        string $stationId,
        Measurement $measurement,
    ): void
    {
        $this->submitMeasurements($stationId, [$measurement]);
    }

    /**
     * @param list<Measurement> $measurements
     */
    public function submitMeasurements(
        string $stationId,
        array $measurements,
    ): void
    {
        $stationId = Assert::notBlank($stationId, 'station ID');
        $measurements = Assert::notEmpty($measurements, 'station measurements');
        $measurements = Assert::allInstancesOf(
            $measurements,
            Measurement::class,
            'station measurement',
        );
        $payload = [];

        foreach ($measurements as $measurement) {
            $payload[] = [
                'station_id' => $stationId,
                ...$measurement->toArray(),
            ];
        }

        // https://openweathermap.org/api/stations#measurement
        $this
            ->endpoint()
            ->json($payload)
            ->post('/data/3.0/measurements');
    }

    /**
     * @return array{
     *     external_id: string,
     *     name: string,
     *     latitude: float,
     *     longitude: float,
     *     altitude: float
     * }
     */
    private static function stationPayload(
        string $externalId,
        string $name,
        float $latitude,
        float $longitude,
        float $altitude,
    ): array {
        return [
            'external_id' => Assert::notBlank($externalId, 'external station ID'),
            'name' => Assert::notBlank($name, 'station name'),
            'latitude' => Assert::latitude($latitude),
            'longitude' => Assert::longitude($longitude),
            'altitude' => Assert::finiteNumber($altitude, 'station altitude'),
        ];
    }
}
