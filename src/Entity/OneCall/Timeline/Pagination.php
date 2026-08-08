<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\Api\Contract\ResolverInterface;
use ProgrammatorDev\OpenWeatherMap\Hydration\OneCall\PaginationUrlNormalizer;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

/**
 * @template TPage of EntityInterface
 */
final class Pagination
{
    /**
     * @param class-string<TPage> $pageClass
     */
    private function __construct(
        private readonly string $pageClass,
        private readonly ?string $previousPageUrl,
        private readonly ?string $nextPageUrl,
        private readonly ?ResolverInterface $resolver,
    ) {}

    /**
     * @param class-string<TPage> $pageClass
     * @return self<TPage>
     */
    public static function fromArray(
        array $data,
        string $pageClass,
        ?Context $context = null,
    ): self {
        $reader = PayloadReader::from($data, self::class);
        $previousPageUrl = $reader->nullableString('prev');
        $nextPageUrl = $reader->nullableString('next');

        return new self(
            pageClass: $pageClass,
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
     * @return TPage|null
     */
    public function nextPage(): ?EntityInterface
    {
        return $this->resolve($this->nextPageUrl);
    }

    /**
     * @return TPage|null
     */
    public function previousPage(): ?EntityInterface
    {
        return $this->resolve($this->previousPageUrl);
    }

    /**
     * @return TPage|null
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

        /** @var TPage $page */
        $page = $this->resolver->entity($pageUrl, $this->pageClass);

        return $page;
    }
}
