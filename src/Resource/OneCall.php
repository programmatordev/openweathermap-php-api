<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Current;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\DayTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\FifteenMinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\HourTimeline;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithLanguage;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithUnits;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class OneCall extends Resource
{
    use WithLanguage;
    use WithUnits;

    public function current(float $latitude, float $longitude): Current
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/one-call-4#current
        /** @var Current $current */
        $current = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/current')
            ->entity(Current::class);

        return $current;
    }

    public function minuteTimeline(float $latitude, float $longitude): MinuteTimeline
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/one-call-4#min
        /** @var MinuteTimeline $timeline */
        $timeline = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/timeline/1min')
            ->entity(MinuteTimeline::class);

        return $timeline;
    }

    public function fifteenMinuteTimeline(
        float $latitude,
        float $longitude,
        ?\DateTimeInterface $startAt = null,
        ?int $count = null,
    ): FifteenMinuteTimeline {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        if ($count !== null) {
            $count = Assert::positiveInteger($count, 'timeline count');
        }

        // https://openweathermap.org/api/one-call-4#15min
        /** @var FifteenMinuteTimeline $timeline */
        $timeline = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startAt?->getTimestamp(),
                'cnt' => $count,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/timeline/15min')
            ->entity(FifteenMinuteTimeline::class);

        return $timeline;
    }

    public function hourTimeline(
        float $latitude,
        float $longitude,
        ?\DateTimeInterface $startAt = null,
        ?int $count = null,
    ): HourTimeline {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        if ($count !== null) {
            $count = Assert::positiveInteger($count, 'timeline count');
        }

        // https://openweathermap.org/api/one-call-4#hourly
        /** @var HourTimeline $timeline */
        $timeline = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startAt?->getTimestamp(),
                'cnt' => $count,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/timeline/1h')
            ->entity(HourTimeline::class);

        return $timeline;
    }

    public function dayTimeline(
        float $latitude,
        float $longitude,
        ?\DateTimeInterface $startAt = null,
        ?int $count = null,
    ): DayTimeline {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        if ($count !== null) {
            $count = Assert::positiveInteger($count, 'timeline count');
        }

        // https://openweathermap.org/api/one-call-4#daily
        /** @var DayTimeline $timeline */
        $timeline = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startAt?->getTimestamp(),
                'cnt' => $count,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/timeline/1day')
            ->entity(DayTimeline::class);

        return $timeline;
    }

    public function alert(string $id): Alert
    {
        $id = Assert::notBlank($id, 'alert ID');

        // https://openweathermap.org/api/one-call-4#alerts
        /** @var Alert $alert */
        $alert = $this
            ->endpoint()
            ->get('/data/4.0/onecall/alert/{id}', [
                'id' => $id,
            ])
            ->entity(Alert::class);

        return $alert;
    }
}
