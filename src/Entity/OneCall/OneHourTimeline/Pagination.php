<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\Api\Contract\ResolverInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline;
use ProgrammatorDev\OpenWeatherMap\Hydration\OneCall\PaginationUrlNormalizer;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Pagination implements EntityInterface
{
    private function __construct(
        private readonly ?string $previousPageUrl,
        private readonly ?string $nextPageUrl,
        private readonly ?ResolverInterface $resolver,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $previousPageUrl = $reader->nullableString('prev');
        $nextPageUrl = $reader->nullableString('next');

        return new self(
            previousPageUrl: $previousPageUrl === null
                ? null
                : PaginationUrlNormalizer::normalize($previousPageUrl),
            nextPageUrl: $nextPageUrl === null
                ? null
                : PaginationUrlNormalizer::normalize($nextPageUrl),
            resolver: $context?->resolver(),
        );
    }

    public function previousPageUrl(): ?string
    {
        return $this->previousPageUrl;
    }

    public function nextPageUrl(): ?string
    {
        return $this->nextPageUrl;
    }

    public function nextPage(): ?OneHourTimeline
    {
        return $this->resolve($this->nextPageUrl);
    }

    public function previousPage(): ?OneHourTimeline
    {
        return $this->resolve($this->previousPageUrl);
    }

    private function resolve(?string $pageUrl): ?OneHourTimeline
    {
        if ($pageUrl === null) {
            return null;
        }

        if ($this->resolver === null) {
            throw new \LogicException(
                'Pagination navigation requires a timeline returned by the API.',
            );
        }

        /** @var OneHourTimeline $timeline */
        $timeline = $this->resolver->entity(
            $pageUrl,
            OneHourTimeline::class,
        );

        return $timeline;
    }
}
