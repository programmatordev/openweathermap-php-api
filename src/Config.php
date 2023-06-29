<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidApplicationKeyException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidLanguageException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidMeasurementSystemException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateApplicationKeyTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateLanguageTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateMeasurementSystemTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    use ValidateApplicationKeyTrait;
    use ValidateMeasurementSystemTrait;
    use ValidateLanguageTrait;

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

    /**
     * @throws InvalidApplicationKeyException
     */
    public function setApplicationKey(string $applicationKey): self
    {
        $this->validateApplicationKey($applicationKey);

        $this->options['applicationKey'] = $applicationKey;

        return $this;
    }

    public function getMeasurementSystem(): string
    {
        return $this->options['measurementSystem'];
    }

    /**
     * @throws InvalidMeasurementSystemException
     */
    public function setMeasurementSystem(string $measurementSystem): self
    {
        $this->validateMeasureSystem($measurementSystem);

        $this->options['measurementSystem'] = $measurementSystem;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->options['language'];
    }

    /**
     * @throws InvalidLanguageException
     */
    public function setLanguage(string $language): self
    {
        $this->validateLanguage($language);

        $this->options['language'] = $language;

        return $this;
    }

    public function getHttpClientBuilder(): HttpClientBuilder
    {
        return $this->options['httpClientBuilder'];
    }
}