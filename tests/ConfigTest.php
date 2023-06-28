<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ConfigTest extends AbstractTest
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config([
            'applicationKey' => self::APPLICATION_KEY
        ]);
    }

    public function testConfigGetApplicationKey()
    {
        $this->assertSame('testappkey', $this->config->getApplicationKey());
    }

    public function testConfigGetHttpClientBuilder()
    {
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
    }

    public function testConfigGetMeasurementSystem()
    {
        $this->assertSame('metric', $this->config->getMeasurementSystem()); // Default value
    }

    public function testConfigGetMeasurementSystemWithConfigValue()
    {
        $config = new Config([
            'applicationKey' => self::APPLICATION_KEY,
            'measurementSystem' => 'imperial'
        ]);

        $this->assertSame('imperial', $config->getMeasurementSystem());
    }

    public function testConfigGetLanguage()
    {
        $this->assertSame('en', $this->config->getLanguage()); // Default value
    }

    public function testConfigGetLanguageWithConfigValue()
    {
        $config = new Config([
            'applicationKey' => self::APPLICATION_KEY,
            'language' => 'pt'
        ]);

        $this->assertSame('pt', $config->getLanguage());
    }

    #[DataProvider('provideInvalidConfigOptionsData')]
    public function testConfigWithInvalidOptions(array $options, string $expectedException)
    {
        $this->expectException($expectedException);

        new Config($options);
    }

    public static function provideInvalidConfigOptionsData(): \Generator
    {
        yield 'missing application key' => [
            [],
            MissingOptionsException::class
        ];
        yield 'empty application key' => [
            [
                'applicationKey' => ''
            ],
            InvalidOptionsException::class
        ];
        yield 'invalid measurement system' => [
            [
                'applicationKey' => self::APPLICATION_KEY,
                'measurementSystem' => 'invalid'
            ],
            InvalidOptionsException::class
        ];
        yield 'invalid language' => [
            [
                'applicationKey' => self::APPLICATION_KEY,
                'language' => 'invalid'
            ],
            InvalidOptionsException::class
        ];
    }
}