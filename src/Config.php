<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    private array $options;

    public function __construct(array $options = [])
    {
        $optionsResolver = new OptionsResolver();

        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'httpClientBuilder' => new HttpClientBuilder(),
            'unit' => Unit::CELSIUS,
            'language' => Language::ENGLISH
        ]);

        $optionsResolver->setRequired('applicationKey');

        $optionsResolver->setAllowedTypes('applicationKey', 'string');
        $optionsResolver->setAllowedTypes('httpClientBuilder', HttpClientBuilder::class);
        $optionsResolver->setAllowedTypes('unit', 'string');
        $optionsResolver->setAllowedTypes('language', 'string');

        $optionsResolver->setAllowedValues('applicationKey', function($value) {
            return !empty($value);
        });
        $optionsResolver->setAllowedValues('unit', Unit::getList());
        $optionsResolver->setAllowedValues('language', Language::getList());
    }

    public function getApplicationKey(): string
    {
        return $this->options['applicationKey'];
    }

    public function getHttpClientBuilder(): HttpClientBuilder
    {
        return $this->options['httpClientBuilder'];
    }

    public function getUnit(): string
    {
        return $this->options['unit'];
    }

    public function getLanguage(): string
    {
        return $this->options['language'];
    }
}