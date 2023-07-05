<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\Validator\BlankValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    use BlankValidatorTrait;
    use ChoiceValidatorTrait;

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
            'measurementSystem' => MeasurementSystem::METRIC,
            'language' => Language::ENGLISH
        ]);

        $optionsResolver->setRequired('applicationKey');

        $optionsResolver->setAllowedTypes('applicationKey', 'string');
        $optionsResolver->setAllowedTypes('httpClientBuilder', HttpClientBuilder::class);
        $optionsResolver->setAllowedTypes('measurementSystem', 'string');
        $optionsResolver->setAllowedTypes('language', 'string');

        $optionsResolver->setAllowedValues('applicationKey', function($value) {
            return !empty($value);
        });
        $optionsResolver->setAllowedValues('measurementSystem', MeasurementSystem::getList());
        $optionsResolver->setAllowedValues('language', Language::getList());
    }

    public function getApplicationKey(): string
    {
        return $this->options['applicationKey'];
    }

    public function setApplicationKey(string $applicationKey): self
    {
        $this->validateBlank('applicationKey', $applicationKey);

        $this->options['applicationKey'] = $applicationKey;

        return $this;
    }

    public function getMeasurementSystem(): string
    {
        return $this->options['measurementSystem'];
    }

    public function setMeasurementSystem(string $measurementSystem): self
    {
        $this->validateChoice('measurementSystem', $measurementSystem, MeasurementSystem::getList());

        $this->options['measurementSystem'] = $measurementSystem;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->options['language'];
    }

    public function setLanguage(string $language): self
    {
        $this->validateChoice('language', $language, Language::getList());

        $this->options['language'] = $language;

        return $this;
    }

    public function getHttpClientBuilder(): HttpClientBuilder
    {
        return $this->options['httpClientBuilder'];
    }
}