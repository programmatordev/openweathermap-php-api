<?php

namespace ProgrammatorDev\OpenWeatherMap\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Decodes the mixed response formats used across OpenWeather products.
 *
 * Most endpoints return JSON, while Weather Maps returns PNG bytes. Maps error
 * responses may also contain JSON despite advertising an image content type,
 * so the payload must be attempted as JSON before its metadata is trusted.
 */
final class PayloadDecoder
{
    public function __invoke(ResponseInterface $response): mixed
    {
        // The response stream may already have been read before a custom SDK
        // decoder is invoked, so always consume it again from the beginning.
        $response->getBody()->rewind();
        $contents = $response->getBody()->getContents();

        if ($contents === '') {
            return null;
        }

        try {
            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $contentType = strtolower($response->getHeaderLine('Content-Type'));

            // Images are successful binary payloads, while non-JSON error
            // bodies still need to reach the HTTP error mapper unchanged.
            if (
                $response->getStatusCode() >= 400
                || str_starts_with($contentType, 'image/')
            ) {
                return $contents;
            }

            // A successful non-image response is expected to be valid JSON;
            // preserve the previous strict failure instead of hiding corruption.
            throw $exception;
        }
    }
}
