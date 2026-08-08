<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\Api\Runtime;
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;
use ProgrammatorDev\OpenWeatherMap\Response\MapTile;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Maps extends Resource
{
    private const BASE_URL = 'https://tile.openweathermap.org';

    public function __construct(
        Runtime $runtime,
        #[\SensitiveParameter] private readonly string $apiKey,
    ) {
        parent::__construct($runtime);
    }

    public function tile(
        MapLayer $layer,
        int $zoom,
        int $x,
        int $y,
    ): MapTile {
        $x = Assert::tileCoordinate($x, $zoom, 'X');
        $y = Assert::tileCoordinate($y, $zoom, 'Y');

        // https://openweathermap.org/api/weathermaps
        $response = $this
            ->endpoint()
            ->get(self::BASE_URL . '/map/{layer}/{zoom}/{x}/{y}.png', [
                'layer' => $layer->value,
                'zoom' => $zoom,
                'x' => $x,
                'y' => $y,
            ]);
        $contents = $response->data();

        if (!is_string($contents)) {
            throw new \UnexpectedValueException(
                'Map tile response body must contain raw image data.',
            );
        }

        $contentType = $response->raw()->getHeaderLine('Content-Type');
        $mediaType = strtolower(trim(explode(';', $contentType, 2)[0]));

        if ($mediaType !== 'image/png') {
            throw new \UnexpectedValueException(
                'Map tile response must use the "image/png" content type.',
            );
        }

        return new MapTile($contents, $contentType);
    }
}
