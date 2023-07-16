<?php

namespace ProgrammatorDev\OpenWeatherMap;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;
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
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'unitSystem' => UnitSystem::METRIC,
            'language' => Language::ENGLISH,
            'httpClientBuilder' => new HttpClientBuilder(),
            'cache' => null,
            'logger' => null
        ]);

        $resolver->setRequired('applicationKey');

        $resolver->setAllowedTypes('applicationKey', 'string');
        $resolver->setAllowedTypes('unitSystem', 'string');
        $resolver->setAllowedTypes('language', 'string');
        $resolver->setAllowedTypes('httpClientBuilder', HttpClientBuilder::class);
        $resolver->setAllowedTypes('cache', ['null', CacheItemPoolInterface::class]);
        $resolver->setAllowedTypes('logger', ['null', LoggerInterface::class]);

        $resolver->setAllowedValues('applicationKey', function($value) {
            return !empty($value);
        });
        $resolver->setAllowedValues('unitSystem', UnitSystem::getList());
        $resolver->setAllowedValues('language', Language::getList());
    }

    public function getApplicationKey(): string
    {
        return $this->options['applicationKey'];
    }

    /**
     * @throws ValidationException
     */
    public function setApplicationKey(string $applicationKey): self
    {
        $this->validateBlank('applicationKey', $applicationKey);

        $this->options['applicationKey'] = $applicationKey;

        return $this;
    }

    public function getUnitSystem(): string
    {
        return $this->options['unitSystem'];
    }

    /**
     * @throws ValidationException
     */
    public function setUnitSystem(string $unitSystem): self
    {
        $this->validateChoice('unitSystem', $unitSystem, UnitSystem::getList());

        $this->options['unitSystem'] = $unitSystem;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->options['language'];
    }

    /**
     * @throws ValidationException
     */
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