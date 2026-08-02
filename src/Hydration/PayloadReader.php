<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration;

use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;

/**
 * Reads and validates response values inside an EntityInterface::fromArray() hydrator.
 * Context-dependent mapping remains the entity's responsibility.
 */
final class PayloadReader
{
    private function __construct(
        private readonly array $payload,
        private readonly string $entity
    ) {}

    public static function from(array $payload, string $entity): self
    {
        return new self($payload, $entity);
    }

    public function nullableString(string $path): ?string
    {
        return $this->nullableValue(
            $path,
            'string',
            static fn(mixed $value): bool => is_string($value)
        );
    }

    public function nullableInt(string $path): ?int
    {
        return $this->nullableValue(
            $path,
            'int',
            static fn(mixed $value): bool => is_int($value)
        );
    }

    public function nullableFloat(string $path): ?float
    {
        // Whole JSON numbers decode as integers even when the field represents
        // a measurement that this library exposes as a float.
        $value = $this->nullableValue(
            $path,
            'int|float',
            static fn(mixed $value): bool => is_int($value) || is_float($value)
        );

        return $value === null ? null : (float) $value;
    }

    public function nullableBool(string $path): ?bool
    {
        return $this->nullableValue(
            $path,
            'bool',
            static fn(mixed $value): bool => is_bool($value)
        );
    }

    public function nullableArray(string $path): ?array
    {
        return $this->nullableValue(
            $path,
            'array',
            static fn(mixed $value): bool => is_array($value)
        );
    }

    /**
     * @return list<string>|null
     */
    public function nullableStringList(string $path): ?array
    {
        $values = $this->nullableArray($path);

        if ($values === null) {
            return null;
        }

        $strings = [];

        foreach ($values as $index => $value) {
            if (!is_string($value)) {
                throw HydrationException::invalidType(
                    $this->entity,
                    sprintf('%s.%s', $path, $index),
                    'string',
                    $value
                );
            }

            $strings[] = $value;
        }

        return $strings;
    }

    public function nullableTimestamp(string $path): ?\DateTimeImmutable
    {
        $timestamp = $this->nullableInt($path);

        if ($timestamp === null) {
            return null;
        }

        return (new \DateTimeImmutable(sprintf('@%d', $timestamp)))
            ->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @param \Closure(mixed): bool $accepts
     */
    private function nullableValue(
        string $path,
        string $expectedType,
        \Closure $accepts
    ): mixed {
        $value = $this->value($path);

        if ($value === null) {
            return null;
        }

        if (!$accepts($value)) {
            throw HydrationException::invalidType(
                $this->entity,
                $path,
                $expectedType,
                $value
            );
        }

        return $value;
    }

    private function value(string $path): mixed
    {
        $value = $this->payload;
        $resolvedPath = [];

        // Dot-separated paths allow concise entities to expose values from
        // nested API payload structures without mirroring every container.
        foreach (explode('.', $path) as $segment) {
            if (!is_array($value)) {
                throw HydrationException::invalidType(
                    $this->entity,
                    implode('.', $resolvedPath),
                    'array',
                    $value
                );
            }

            if (!array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
            $resolvedPath[] = $segment;

            if ($value === null) {
                return null;
            }
        }

        return $value;
    }
}
