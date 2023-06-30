<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class Alert
{
    private string $senderName;

    private string $eventName;

    private \DateTimeImmutable $startsAt;

    private \DateTimeImmutable $endsAt;

    private string $description;

    private array $tags;

    public function __construct(array $data)
    {
        $timezoneUtc = new \DateTimeZone('UTC');

        $this->senderName = $data['sender_name'];
        $this->eventName = $data['event'];
        $this->startsAt = \DateTimeImmutable::createFromFormat('U', $data['start'], $timezoneUtc);
        $this->endsAt = \DateTimeImmutable::createFromFormat('U', $data['end'], $timezoneUtc);
        $this->description = $data['description'];
        $this->tags = $data['tags'];
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function getEndsAt(): \DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}