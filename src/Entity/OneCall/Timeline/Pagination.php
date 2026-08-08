<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\Api\Contract\ResolverInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\OneCall\PaginationUrlNormalizer;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

/**
 * @template TTimeline of EntityInterface
 */
final class Pagination
{
    /**
     * @param class-string<TTimeline> $timelineClass
     */
    private function __construct(
        private readonly string $timelineClass,
        private readonly ?string $previousPageUrl,
        private readonly ?string $nextPageUrl,
        private readonly ?ResolverInterface $resolver,
    ) {}

    /**
     * @param class-string<TTimeline> $timelineClass
     * @return self<TTimeline>
     */
    public static function fromArray(
        array $data,
        string $timelineClass,
        ?Context $context = null,
    ): self {
        $reader = PayloadReader::from($data, self::class);
        $previousPageUrl = $reader->nullableString('prev');
        $nextPageUrl = $reader->nullableString('next');

        return new self(
            timelineClass: $timelineClass,
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

    /**
     * @return TTimeline|null
     */
    public function nextPage(): ?EntityInterface
    {
        return $this->resolve($this->nextPageUrl);
    }

    /**
     * @return TTimeline|null
     */
    public function previousPage(): ?EntityInterface
    {
        return $this->resolve($this->previousPageUrl);
    }

    /**
     * @return TTimeline|null
     */
    private function resolve(?string $pageUrl): ?EntityInterface
    {
        if ($pageUrl === null) {
            return null;
        }

        if ($this->resolver === null) {
            throw new \LogicException(
                'Pagination navigation requires a timeline returned by the API.',
            );
        }

        /** @var TTimeline $timeline */
        $timeline = $this->resolver->entity($pageUrl, $this->timelineClass);

        return $timeline;
    }
}
