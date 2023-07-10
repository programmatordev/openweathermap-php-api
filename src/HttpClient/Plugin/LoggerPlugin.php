<?php

namespace ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Http\Message\Formatter;
use Http\Message\Formatter\SimpleFormatter;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LoggerPlugin implements Plugin
{
    private LoggerInterface $logger;

    private Formatter $formatter;

    public function __construct(LoggerInterface $logger, Formatter $formatter = null)
    {
        $this->logger = $logger;
        $this->formatter = $formatter ?: new SimpleFormatter();
    }

    /**
     * @throws Exception
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $start = hrtime(true) / 1E6;
        $this->logger->info(
            \sprintf('Sending request: %s', $this->formatter->formatRequest($request))
        );

        return $next($request)->then(function (ResponseInterface $response) use ($start, $request) {
            $milliseconds = (int) round(hrtime(true) / 1E6 - $start);
            $formattedResponse = $this->formatter->formatResponseForRequest($response, $request);

            $this->logger->info(
                \sprintf('Received response: %s', $formattedResponse), [
                    'milliseconds' => $milliseconds
                ]
            );

            return $response;
        }, function (Exception $exception) use ($request, $start) {
            $milliseconds = (int) round((hrtime(true) / 1E6 - $start));

            if ($exception instanceof Exception\HttpException) {
                $formattedResponse = $this->formatter->formatResponseForRequest(
                    $exception->getResponse(),
                    $exception->getRequest()
                );

                $this->logger->error(
                    \sprintf('Error: %s with response: %s', $exception->getMessage(), $formattedResponse), [
                        'exception' => $exception,
                        'milliseconds' => $milliseconds
                    ]
                );
            }
            else {
                $this->logger->error(
                    \sprintf(
                        'Error: %s when sending request: %s',
                        $exception->getMessage(),
                        $this->formatter->formatRequest($request)
                    ), [
                        'exception' => $exception,
                        'milliseconds' => $milliseconds
                    ]
                );
            }

            throw $exception;
        });
    }
}