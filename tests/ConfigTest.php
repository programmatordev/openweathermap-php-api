<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Monolog\Logger;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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

    public function testConfigDefaultOptions()
    {
        $this->assertSame(self::APPLICATION_KEY, $this->config->getApplicationKey());
        $this->assertSame('metric', $this->config->getMeasurementSystem());
        $this->assertSame('en', $this->config->getLanguage());
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
        $this->assertSame(null, $this->config->getCache());
        $this->assertSame(null, $this->config->getLogger());
    }

    public function testConfigWithOptions()
    {
        $config = new Config([
            'applicationKey' => 'newtestappkey',
            'measurementSystem' => 'imperial',
            'language' => 'pt',
            'httpClientBuilder' => new HttpClientBuilder(),
            'cache' => new FilesystemAdapter(),
            'logger' => new Logger('test')
        ]);

        $this->assertSame('newtestappkey', $config->getApplicationKey());
        $this->assertSame('imperial', $config->getMeasurementSystem());
        $this->assertSame('pt', $config->getLanguage());
        $this->assertInstanceOf(HttpClientBuilder::class, $config->getHttpClientBuilder());
        $this->assertInstanceOf(CacheItemPoolInterface::class, $config->getCache());
        $this->assertInstanceOf(LoggerInterface::class, $config->getLogger());
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

    public function testConfigSetApplicationKey()
    {
        $this->config->setApplicationKey('newtestappkey');
        $this->assertSame('newtestappkey', $this->config->getApplicationKey());
    }

    public function testConfigSetApplicationKeyWithBlankValue()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->config->setApplicationKey('');
    }

    public function testConfigSetMeasurementSystem()
    {
        $this->config->setMeasurementSystem('imperial');
        $this->assertSame('imperial', $this->config->getMeasurementSystem());
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidMeasurementSystemData')]
    public function testConfigSetMeasurementSystemWithInvalidValue(string $measurementSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->config->setMeasurementSystem($measurementSystem);
    }

    public function testConfigSetLanguage()
    {
        $this->config->setLanguage('pt');
        $this->assertSame('pt', $this->config->getLanguage());
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidLanguageData')]
    public function testConfigSetLanguageWithInvalidValue(string $language, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->config->setLanguage($language);
    }

    public function testConfigSetHttpClientBuilder()
    {
        $this->config->setHttpClientBuilder(new HttpClientBuilder());
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
    }

    public function testConfigSetCache()
    {
        $this->config->setCache(new FilesystemAdapter());
        $this->assertInstanceOf(CacheItemPoolInterface::class, $this->config->getCache());
    }

    public function testConfigSetLogger()
    {
        $this->config->setLogger(new Logger('test'));
        $this->assertInstanceOf(LoggerInterface::class, $this->config->getLogger());
    }
}