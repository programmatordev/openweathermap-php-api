<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Support;

final class Fixture
{
    public static function contents(string $path): string
    {
        $contents = file_get_contents(sprintf('%s/../Fixtures/%s', __DIR__, $path));

        if ($contents === false) {
            throw new \RuntimeException(sprintf('Unable to read fixture "%s".', $path));
        }

        return $contents;
    }

    public static function json(string $path): array
    {
        $contents = self::contents($path);

        $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \UnexpectedValueException(sprintf(
                'Fixture "%s" must decode to an array.',
                $path,
            ));
        }

        return $data;
    }
}
