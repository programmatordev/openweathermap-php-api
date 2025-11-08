<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\Answer;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use ProgrammatorDev\OpenWeatherMap\Test\MockResponse;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestItemResponseTrait;

class AssistantResourceTest extends AbstractTest
{
    use TestItemResponseTrait;

    public static function provideItemResponseData(): \Generator
    {
        yield 'start session' => [
            Answer::class,
            MockResponse::ASSISTANT_START_SESSION,
            'assistant',
            'startSession',
            ['prompt']
        ];
    }
}