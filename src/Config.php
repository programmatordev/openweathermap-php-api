<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin\LoggerPlugin;
use ProgrammatorDev\OpenWeatherMap\Validator\BlankValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    use BlankValidatorTrait;
    use ChoiceValidatorTrait;

    private array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        $this->configureAware();
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'measurementSystem' => MeasurementSystem::METRIC,
            'language' => Language::ENGLISH,
            'httpClientBuilder' => new HttpClientBuilder(),
            'cache' => null,
            'logger' => null
        ]);

        $resolver->setRequired('applicationKey');

        $resolver->setAllowedTypes('applicationKey', 'string');
        $resolver->setAllowedTypes('measurementSystem', 'string');
        $resolver->setAllowedTypes('language', 'string');
        $resolver->setAllowedTypes('httpClientBuilder', HttpClientBuilder::class);
        $resolver->setAllowedTypes('cache', ['null', CacheItemPoolInterface::class]);
        $resolver->setAllowedTypes('logger', ['null', LoggerInterface::class]);

        $resolver->setAllowedValues('applicationKey', function($value) {
            return !empty($value);
        });
        $resolver->setAllowedValues('measurementSystem', MeasurementSystem::getList());
        $resolver->setAllowedValues('language', Language::getList());
    }

    private function configureAware(): void
    {
        if ($this->getLogger() !== null) {
            $this->getHttpClientBuilder()->addPlugin(
                new LoggerPlugin($this->getLogger())
            );
        }
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

    public function setHttpClientBuilder(HttpClientBuilder $httpClientBuilder): self
    {
        $this->options['httpClientBuilder'] = $httpClientBuilder;

        return $this;
    }

    public function getCache(): ?CacheItemPoolInterface
    {
        return $this->options['cache'];
    }

    public function setCache(?CacheItemPoolInterface $cache): self
    {
        $this->options['cache'] = $cache;

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->options['logger'];
    }

    public function setLogger(?LoggerInterface $logger): self
    {
        $this->options['logger'] = $logger;

        return $this;
    }
}