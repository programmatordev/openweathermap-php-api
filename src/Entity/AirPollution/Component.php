<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

class Component
{
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
        $this->carbonMonoxide = $data['co'];
        $this->nitrogenMonoxide = $data['no'];
        $this->nitrogenDioxide = $data['no2'];
        $this->ozone = $data['o3'];
        $this->sulphurDioxide = $data['so2'];
        $this->fineParticulateMatter = $data['pm2_5'];
        $this->coarseParticulateMatter = $data['pm10'];
        $this->ammonia = $data['nh3'];
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