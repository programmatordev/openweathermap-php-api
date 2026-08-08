<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\Api\Runtime;
use ProgrammatorDev\OpenWeatherMap\Enum\MapLayer;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Response\MapTile;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Maps extends Resource
{
    // https://openweathermap.org/api/weathermaps
    private const TILE_URL = 'https://tile.openweathermap.org/map/%s/%d/%d/%d.png';

    public function __construct(
        Runtime $runtime,
        #[\SensitiveParameter] private readonly string $apiKey,
    ) {
        parent::__construct($runtime);
    }

    public function tileUrl(
        MapLayer $layer,
        int $zoom,
        int $x,
        int $y,
    ): string {
        return sprintf(
            '%s?%s=%s',
            $this->tileEndpointUrl($layer, $zoom, $x, $y),
            OpenWeatherMap::AUTHENTICATION_KEY,
            rawurlencode($this->apiKey),
        );
    }

    public function tile(
        MapLayer $layer,
        int $zoom,
        int $x,
        int $y,
    ): MapTile {
        $response = $this
            ->endpoint()
            ->get($this->tileEndpointUrl($layer, $zoom, $x, $y));
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

    private function tileEndpointUrl(
        MapLayer $layer,
        int $zoom,
        int $x,
        int $y,
    ): string {
        $x = Assert::tileCoordinate($x, $zoom, 'X');
        $y = Assert::tileCoordinate($y, $zoom, 'Y');

        return sprintf(
            self::TILE_URL,
            rawurlencode($layer->value),
            $zoom,
            $x,
            $y,
        );
    }
}
