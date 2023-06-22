<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    private array $config;

    public function __construct(array $config = [])
    {
        $optionsResolver = new OptionsResolver();

        $this->configureOptions($optionsResolver);
        $this->config = $optionsResolver->resolve($config);

        if (empty($this->config['applicationKey'])) {
            throw new \InvalidArgumentException('The option "applicationKey" must not be empty.');
        }
    }

    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'httpClientBuilder' => new HttpClientBuilder()
        ]);

        $optionsResolver->setRequired('applicationKey');

        $optionsResolver->setAllowedTypes('applicationKey', 'string');
        $optionsResolver->setAllowedTypes('httpClientBuilder', HttpClientBuilder::class);
    }

    public function getApplicationKey(): string
    {
        return $this->config['applicationKey'];
    }

    public function getHttpClientBuilder(): HttpClientBuilder
    {
        return $this->config['httpClientBuilder'];
    }
}