<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

use ProgrammatorDev\Api\Context\ErrorContext;

abstract class ApiException extends \RuntimeException
{
    protected function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly ?int $apiCode,
        private readonly mixed $responseData
    ) {
        parent::__construct($message);
    }

    public static function fromContext(ErrorContext $context): static
    {
        $data = $context->response()->data();
        $statusCode = $context->statusCode();

        return new static(
            message: self::resolveMessage(
                statusCode: $statusCode,
                reasonPhrase: $context->response()->raw()->getReasonPhrase(),
                data: $data
            ),
            statusCode: $statusCode,
            apiCode: self::resolveApiCode($data),
            responseData: $data
        );
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function apiCode(): ?int
    {
        return $this->apiCode;
    }

    public function responseData(): mixed
    {
        return $this->responseData;
    }

    private static function resolveMessage(
        int $statusCode,
        string $reasonPhrase,
        mixed $data
    ): string {
        if (is_array($data)) {
            $message = $data['message'] ?? null;

            if (is_string($message) && trim($message) !== '') {
                return $message;
            }
        }

        if (is_string($data) && trim($data) !== '') {
            return $data;
        }

        if (trim($reasonPhrase) !== '') {
            return sprintf(
                'OpenWeather API request failed with HTTP %d (%s).',
                $statusCode,
                $reasonPhrase
            );
        }

        return sprintf(
            'OpenWeather API request failed with HTTP %d.',
            $statusCode
        );
    }

    private static function resolveApiCode(mixed $data): ?int
    {
        if (!is_array($data)) {
            return null;
        }

        // OpenWeather APIs use both keys, and some legacy responses encode numeric codes as strings.
        // Keep the public API consistently nullable-int.
        foreach (['cod', 'code'] as $key) {
            $code = $data[$key] ?? null;

            if (is_int($code)) {
                return $code;
            }

            if (is_string($code) && preg_match('/^\d+$/', $code) === 1) {
                return (int) $code;
            }
        }

        return null;
    }
}
