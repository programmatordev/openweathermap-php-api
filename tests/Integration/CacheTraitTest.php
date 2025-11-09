<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\Api\Builder\CacheBuilder;
use ProgrammatorDev\OpenWeatherMap\Resource\Resource;
use ProgrammatorDev\OpenWeatherMap\Resource\Util\CacheTrait;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use Psr\Cache\CacheItemPoolInterface;

class CacheTraitTest extends AbstractTest
{
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $cacheBuilder = new CacheBuilder($pool);

        $this->api->setCacheBuilder($cacheBuilder);

        $this->resource = new class($this->api) extends Resource {
            use CacheTrait;

            public function getCacheTtl(): ?int
            {
                return $this->api->getCacheBuilder()?->getTtl();
            }
        };
    }

    public function testMethods(): void
    {
        $this->assertSame(60, $this->resource->getCacheTtl());
        $this->assertSame(600, $this->resource->withCacheTtl(600)->getCacheTtl());
        $this->assertSame(60, $this->resource->getCacheTtl()); // back to default value
    }
}