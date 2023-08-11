<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

trait WithUnitSystemTrait
{
    use ChoiceValidatorTrait;

    /**
     * @throws ValidationException
     */
    public function withUnitSystem(string $unitSystem): static
    {
        Validator::choice(UnitSystem::getList())->assert($unitSystem, 'unitSystem');

        $clone = clone $this;
        $clone->unitSystem = $unitSystem;

        return $clone;
    }

    public function getUnitSystem(): string
    {
        return $this->unitSystem;
    }
}