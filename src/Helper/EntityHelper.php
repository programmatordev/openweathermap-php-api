<?php

namespace ProgrammatorDev\OpenWeatherMap\Helper;

class EntityHelper
{
    public static function createEntityList(string $entityClass, array $list): array
    {
        return array_map(function(array $data) use ($entityClass) {
            return new $entityClass($data);
        }, $list);
    }

    public static function createEntityKeyList(string $entityClass, array $list): array
    {
        return array_map(function(array $data, int|string $key) use ($entityClass) {
            return new $entityClass($key, $data);
        }, $list, array_keys($list));
    }
}