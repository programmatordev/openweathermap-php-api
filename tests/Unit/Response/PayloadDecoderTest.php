<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Response;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Response\PayloadDecoder;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PayloadDecoderTest extends TestCase
{
    private PayloadDecoder $decoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decoder = new PayloadDecoder();
    }

    public function testItDecodesJsonRegardlessOfTheContentType(): void
    {
        $response = new Response(
            status: 401,
            headers: ['Content-Type' => 'image/png'],
            body: Fixture::contents('maps/tile/missing-key.json'),
        );

        self::assertSame(
            Fixture::json('maps/tile/missing-key.json'),
            ($this->decoder)($response),
        );
    }

    public function testItReturnsBinaryBodiesUnchanged(): void
    {
        $contents = Fixture::contents('maps/tile/clouds-new.png');
        $response = new Response(
            headers: ['Content-Type' => 'image/png'],
            body: $contents,
        );

        self::assertSame($contents, ($this->decoder)($response));
    }

    public function testItReturnsPlainTextBodiesUnchanged(): void
    {
        $response = new Response(status: 503, body: 'Service unavailable');

        self::assertSame('Service unavailable', ($this->decoder)($response));
    }

    public function testItRejectsMalformedSuccessfulJson(): void
    {
        $response = new Response(
            headers: ['Content-Type' => 'application/json'],
            body: '{"incomplete":',
        );

        $this->expectException(\JsonException::class);

        ($this->decoder)($response);
    }

    public function testItReturnsNullForAnEmptyBody(): void
    {
        self::assertNull(($this->decoder)(new Response()));
    }

    public function testItRewindsTheResponseBodyBeforeReading(): void
    {
        $response = new Response(body: '{"ok":true}');
        $response->getBody()->getContents();

        self::assertSame(['ok' => true], ($this->decoder)($response));
    }
}
