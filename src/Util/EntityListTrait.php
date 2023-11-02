<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait EntityListTrait
{
    private function createEntityList(string $entityClass, array $data): array
    {
        return \array_map(function(array $value) use ($entityClass) {
            return new $entityClass($value);
        }, $data);
    }
}