<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait CreateEntityListTrait
{
    private function createEntityList(array $data, string $entityClass): array
    {
        return \array_map(function(array $entityData) use ($entityClass) {
            return new $entityClass($entityData);
        }, $data);
    }
}