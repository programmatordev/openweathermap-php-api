<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;
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

    #[DataProvider('provideInvalidConfigOptionsData')]
    public function testConfigWithInvalidOptions(array $options, string $expectedException)
    {
        $this->expectException($expectedException);

        new Config($options);
    }

    public function testConfigGetApplicationKey()
    {
        $this->assertSame(self::APPLICATION_KEY, $this->config->getApplicationKey());
    }

    public function testConfigSetApplicationKey()
    {
        $this->assertSame(self::APPLICATION_KEY, $this->config->getApplicationKey());

        $this->config->setApplicationKey('newtestappkey');
        $this->assertSame('newtestappkey', $this->config->getApplicationKey());
    }

    public function testConfigSetApplicationKeyWithBlankValue()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->config->setApplicationKey('');
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

    public function testConfigSetMeasurementSystem()
    {
        $this->assertSame('metric', $this->config->getMeasurementSystem());

        $this->config->setMeasurementSystem('imperial');
        $this->assertSame('imperial', $this->config->getMeasurementSystem());
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidMeasurementSystemData')]
    public function testConfigSetMeasurementSystemWithInvalidValue(string $measurementSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->config->setMeasurementSystem($measurementSystem);
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

    public function testConfigSetLanguage()
    {
        $this->assertSame('en', $this->config->getLanguage());

        $this->config->setLanguage('pt');
        $this->assertSame('pt', $this->config->getLanguage());
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidLanguageData')]
    public function testConfigSetLanguageWithInvalidValue(string $language, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->config->setLanguage($language);
    }

    public function testConfigGetHttpClientBuilder()
    {
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
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