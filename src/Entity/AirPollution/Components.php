<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

/**
 * All component concentrations use the fixed unit documented by OpenWeather.
 *
 * @see https://openweathermap.org/api/air-pollution
 */
final class Components implements EntityInterface
{
    private function __construct(
        private readonly ?float $carbonMonoxide,
        private readonly ?float $nitrogenMonoxide,
        private readonly ?float $nitrogenDioxide,
        private readonly ?float $ozone,
        private readonly ?float $sulphurDioxide,
        private readonly ?float $fineParticulateMatter,
        private readonly ?float $coarseParticulateMatter,
        private readonly ?float $ammonia,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            carbonMonoxide: $reader->nullableFloat('co'),
            nitrogenMonoxide: $reader->nullableFloat('no'),
            nitrogenDioxide: $reader->nullableFloat('no2'),
            ozone: $reader->nullableFloat('o3'),
            sulphurDioxide: $reader->nullableFloat('so2'),
            fineParticulateMatter: $reader->nullableFloat('pm2_5'),
            coarseParticulateMatter: $reader->nullableFloat('pm10'),
            ammonia: $reader->nullableFloat('nh3'),
        );
    }

    /**
     * CO (carbon monoxide) concentration.
     */
    public function carbonMonoxide(): ?float
    {
        return $this->carbonMonoxide;
    }

    public function carbonMonoxideUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function carbonMonoxideWithUnit(): ?string
    {
        return $this->format($this->carbonMonoxide);
    }

    /**
     * NO (nitrogen monoxide) concentration.
     */
    public function nitrogenMonoxide(): ?float
    {
        return $this->nitrogenMonoxide;
    }

    public function nitrogenMonoxideUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function nitrogenMonoxideWithUnit(): ?string
    {
        return $this->format($this->nitrogenMonoxide);
    }

    /**
     * NO2 (nitrogen dioxide) concentration.
     */
    public function nitrogenDioxide(): ?float
    {
        return $this->nitrogenDioxide;
    }

    public function nitrogenDioxideUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function nitrogenDioxideWithUnit(): ?string
    {
        return $this->format($this->nitrogenDioxide);
    }

    /**
     * O3 (ozone) concentration.
     */
    public function ozone(): ?float
    {
        return $this->ozone;
    }

    public function ozoneUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function ozoneWithUnit(): ?string
    {
        return $this->format($this->ozone);
    }

    /**
     * SO2 (sulphur dioxide) concentration.
     */
    public function sulphurDioxide(): ?float
    {
        return $this->sulphurDioxide;
    }

    public function sulphurDioxideUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function sulphurDioxideWithUnit(): ?string
    {
        return $this->format($this->sulphurDioxide);
    }

    /**
     * PM2.5 (fine particulate matter) concentration.
     */
    public function fineParticulateMatter(): ?float
    {
        return $this->fineParticulateMatter;
    }

    public function fineParticulateMatterUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function fineParticulateMatterWithUnit(): ?string
    {
        return $this->format($this->fineParticulateMatter);
    }

    /**
     * PM10 (coarse particulate matter) concentration.
     */
    public function coarseParticulateMatter(): ?float
    {
        return $this->coarseParticulateMatter;
    }

    public function coarseParticulateMatterUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function coarseParticulateMatterWithUnit(): ?string
    {
        return $this->format($this->coarseParticulateMatter);
    }

    /**
     * NH3 (ammonia) concentration.
     */
    public function ammonia(): ?float
    {
        return $this->ammonia;
    }

    public function ammoniaUnit(): Unit
    {
        return $this->concentrationUnit();
    }

    public function ammoniaWithUnit(): ?string
    {
        return $this->format($this->ammonia);
    }

    private function concentrationUnit(): Unit
    {
        return Unit::MICROGRAMS_PER_CUBIC_METER;
    }

    private function format(?float $concentration): ?string
    {
        return MeasurementFormatter::format(
            $concentration,
            $this->concentrationUnit(),
        );
    }
}
