<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ClassConstantsTrait
{
    private function getClassConstants(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $constants = $reflection->getConstants();

        // Sort by alphabetical order
        // to be more intuitive when listing values for error messages
        \asort($constants);

        return $constants;
    }
}