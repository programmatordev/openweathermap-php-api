<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline\Pagination;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline\TimelinePage;

final class OneHourTimeline implements EntityInterface
{
    /**
     * @param TimelinePage<Period> $page
     * @param Pagination<self> $pagination
     */
    private function __construct(
        private readonly TimelinePage $page,
        private readonly Pagination $pagination,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $page = TimelinePage::fromArray(
            data: $data,
            entity: self::class,
            periodClass: Period::class,
            context: $context,
        );

        return new self(
            page: $page,
            pagination: Pagination::fromArray(
                data: $data,
                timelineClass: self::class,
                context: $context,
            ),
        );
    }

    public function coordinates(): ?Coordinates
    {
        return $this->page->coordinates();
    }

    public function timezone(): ?Timezone
    {
        return $this->page->timezone();
    }

    /**
     * @return list<Period>
     */
    public function periods(): array
    {
        return $this->page->periods();
    }

    /**
     * @return Pagination<self>
     */
    public function pagination(): Pagination
    {
        return $this->pagination;
    }
}
