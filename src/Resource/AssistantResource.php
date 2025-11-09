<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use Http\Message\Authentication\Header;
use ProgrammatorDev\Api\Method;
use ProgrammatorDev\OpenWeatherMap\Entity\Assistant\Answer;
use Psr\Http\Client\ClientExceptionInterface;

class AssistantResource extends Resource
{
    /**
     * Start a new session (create a new conversation) with the Weather AI Assistant
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

    /**
     * Resume a session (continue a conversation) with the Weather AI Assistant
     *
     * @throws ClientExceptionInterface
     */
    public function resumeSession(string $sessionId, string $prompt): Answer
    {
        $this->api->setAuthentication(new Header('X-Api-Key', $this->api->apiKey));

        $data = $this->api->request(
            method: Method::POST,
            path: $this->api->buildPath('/assistant/session/{sessionId}', [
                'sessionId' => $sessionId
            ]),
            body: json_encode(['prompt' => $prompt])
        );

        return new Answer($data);
    }
}