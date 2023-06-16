<?php

namespace ProgrammatorDev\OpenWeatherMap\HttpClient;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    public static function toArray(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}