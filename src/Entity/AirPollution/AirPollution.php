<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

class AirPollution
{
    private \DateTimeImmutable $dateTime;

    private AirQuality $airQuality;

    private float $carbonMonoxide;

    private float $nitrogenMonoxide;

    private float $nitrogenDioxide;

    private float $ozone;

    private float $sulphurDioxide;

    private float $fineParticulateMatter;

    private float $coarseParticulateMatter;

    private float $ammonia;

    public function __construct(array $data)
    {
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt'], new \DateTimeZone('UTC'));
        $this->airQuality = new AirQuality($data['main']);
        $this->carbonMonoxide = $data['components']['co'];
        $this->nitrogenMonoxide = $data['components']['no'];
        $this->nitrogenDioxide = $data['components']['no2'];
        $this->ozone = $data['components']['o3'];
        $this->sulphurDioxide = $data['components']['so2'];
        $this->fineParticulateMatter = $data['components']['pm2_5'];
        $this->coarseParticulateMatter = $data['components']['pm10'];
        $this->ammonia = $data['components']['nh3'];
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getAirQuality(): AirQuality
    {
        return $this->airQuality;
    }

    public function getCarbonMonoxide(): float
    {
        return $this->carbonMonoxide;
    }

    public function getNitrogenMonoxide(): float
    {
        return $this->nitrogenMonoxide;
    }

    public function getNitrogenDioxide(): float
    {
        return $this->nitrogenDioxide;
    }

    public function getOzone(): float
    {
        return $this->ozone;
    }

    public function getSulphurDioxide(): float
    {
        return $this->sulphurDioxide;
    }

    public function getFineParticulateMatter(): float
    {
        return $this->fineParticulateMatter;
    }

    public function getCoarseParticulateMatter(): float
    {
        return $this->coarseParticulateMatter;
    }

    public function getAmmonia(): float
    {
        return $this->ammonia;
    }
}