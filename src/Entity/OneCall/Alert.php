<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Alert\LocalizedDescription;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Alert implements EntityInterface
{
    /**
     * @param list<LocalizedDescription> $descriptions
     * @param list<string> $tags
     */
    private function __construct(
        private readonly ?string $id,
        private readonly ?string $senderName,
        private readonly ?string $event,
        private readonly ?\DateTimeImmutable $startsAt,
        private readonly ?\DateTimeImmutable $endsAt,
        private readonly array $descriptions,
        private readonly array $tags,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $descriptions = self::hydrateDescriptions($data, $context);

        return new self(
            id: $reader->nullableString('id'),
            senderName: $reader->nullableString('sender_name'),
            event: $reader->nullableString('event'),
            startsAt: $reader->nullableTimestamp('start'),
            endsAt: $reader->nullableTimestamp('end'),
            descriptions: $descriptions,
            tags: $reader->nullableStringList('tags') ?? [],
        );
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function senderName(): ?string
    {
        return $this->senderName;
    }

    public function event(): ?string
    {
        return $this->event;
    }

    public function startsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function endsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    /**
     * @return list<LocalizedDescription>
     */
    public function descriptions(): array
    {
        return $this->descriptions;
    }

    public function description(string $languageCode): ?string
    {
        foreach ($this->descriptions as $description) {
            if ($description->languageCode() === $languageCode) {
                return $description->text();
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    /**
     * @return list<LocalizedDescription>
     */
    private static function hydrateDescriptions(array $data, ?Context $context): array
    {
        if (!array_key_exists('description', $data) || $data['description'] === null) {
            return [];
        }

        // The official contract documents one string, while live responses return
        // localized object arrays: https://openweathermap.org/api/one-call-4
        if (is_string($data['description'])) {
            return [LocalizedDescription::fromArray([
                'description' => $data['description'],
            ], $context)];
        }

        if (!is_array($data['description'])) {
            throw HydrationException::invalidType(
                self::class,
                'description',
                'string|array',
                $data['description'],
            );
        }

        $descriptions = [];

        foreach ($data['description'] as $index => $description) {
            if (!is_array($description)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('description.%s', $index),
                    'array',
                    $description,
                );
            }

            $descriptions[] = LocalizedDescription::fromArray($description, $context);
        }

        return $descriptions;
    }
}
