<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;

trait HasWeatherMeasurements
{
    public function temperature(): ?float
    {
        return $this->temperature;
    }

    public function temperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function temperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->temperature);
    }

    public function feelsLikeTemperature(): ?float
    {
        return $this->feelsLikeTemperature;
    }

    public function feelsLikeTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function feelsLikeTemperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->feelsLikeTemperature);
    }

    public function minimumTemperature(): ?float
    {
        return $this->minimumTemperature;
    }

    public function minimumTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function minimumTemperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->minimumTemperature);
    }

    public function maximumTemperature(): ?float
    {
        return $this->maximumTemperature;
    }

    public function maximumTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function maximumTemperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->maximumTemperature);
    }

    public function pressure(): ?int
    {
        return $this->pressure;
    }

    public function pressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function pressureWithUnit(): ?string
    {
        return $this->formatPressure($this->pressure);
    }

    public function humidity(): ?int
    {
        return $this->humidity;
    }

    public function humidityUnit(): Unit
    {
        return Unit::PERCENT;
    }

    public function humidityWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->humidity, $this->humidityUnit());
    }

    public function seaLevelPressure(): ?int
    {
        return $this->seaLevelPressure;
    }

    public function seaLevelPressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function seaLevelPressureWithUnit(): ?string
    {
        return $this->formatPressure($this->seaLevelPressure);
    }

    public function groundLevelPressure(): ?int
    {
        return $this->groundLevelPressure;
    }

    public function groundLevelPressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function groundLevelPressureWithUnit(): ?string
    {
        return $this->formatPressure($this->groundLevelPressure);
    }

    public function visibility(): ?int
    {
        return $this->visibility;
    }

    public function visibilityUnit(): Unit
    {
        return Unit::METER;
    }

    public function visibilityWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->visibility, $this->visibilityUnit());
    }

    private function formatTemperature(?float $temperature): ?string
    {
        return MeasurementFormatter::format($temperature, $this->units->temperatureUnit());
    }

    private function formatPressure(?int $pressure): ?string
    {
        return MeasurementFormatter::format($pressure, Unit::HECTOPASCAL);
    }
}
