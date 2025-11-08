<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use Http\Message\Authentication\Header;
use ProgrammatorDev\Api\Method;
use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\Answer;
use Psr\Http\Client\ClientExceptionInterface;

class AssistantResource extends Resource
{
    /**
     * Start a new session with the Weather AI Assistant
     *
     * @throws ClientExceptionInterface
     */
    public function startSession(string $prompt): Answer
    {
        $this->api->setAuthentication(new Header('X-Api-Key', $this->api->apiKey));

        $data = $this->api->request(
            method: Method::POST,
            path: '/assistant/session',
            body: json_encode(['prompt' => $prompt])
        );

        return new Answer($data);
    }
}