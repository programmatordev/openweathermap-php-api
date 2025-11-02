<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait EntityTrait
{
    private function createEntityList(string $entityClass, array $list): array
    {
        return array_map(function(array $data) use ($entityClass) {
            return new $entityClass($data);
        }, $list);
    }
}