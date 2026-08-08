<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline\TimelinePage;

final class OneDayTimeline implements EntityInterface
{
    /**
     * @param TimelinePage<Period> $page
     */
    private function __construct(
        private readonly TimelinePage $page,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        return new self(TimelinePage::fromArray(
            data: $data,
            entity: self::class,
            periodClass: Period::class,
            context: $context,
        ));
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
}
