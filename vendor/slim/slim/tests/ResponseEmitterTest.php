<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests;

use Slim\ResponseEmitter;
use Slim\Tests\Assets\HeaderStack;
use Slim\Tests\Mocks\MockStream;
use Slim\Tests\Mocks\SmallChunksStream;

class ResponseEmitterTest extends TestCase
{
    public function setUp()
    {
        HeaderStack::reset();
    }

    public function tearDown()
    {
        HeaderStack::reset();
    }

    public function testRespond()
    {
        $response = $this->createResponse();
        $response->getBody()->write('Hello');

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);

        $this->expectOutputString('Hello');
    }

    public function testRespondNoContent()
    {
        $response = $this->createResponse();

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);

        $this->assertEquals(false, HeaderStack::has('Content-Type'));
        $this->assertEquals(false, HeaderStack::has('Content-Length'));
        $this->expectOutputString('');
    }

    public function testRespondWithPaddedStreamFilterOutput()
    {
        $availableFilter = stream_get_filters();

        $filterName = 'string.rot13';
        $unfilterName = 'string.rot13';
        $specificFilterName = 'string.rot13';
        $specificUnfilterName = 'string.rot13';

        if (in_array($filterName, $availableFilter) && in_array($unfilterName, $availableFilter)) {
            $key = base64_decode('xxxxxxxxxxxxxxxx');
            $iv = base64_decode('Z6wNDk9LogWI4HYlRu0mng==');

            $data = 'Hello';
            $length = strlen($data);

            $stream = fopen('php://temp', 'r+');
            $filter = stream_filter_append($stream, $specificFilterName, STREAM_FILTER_WRITE, [
                'key' => $key,
                'iv' => $iv
            ]);

            fwrite($stream, $data);
            rewind($stream);
            stream_filter_remove($filter);
            stream_filter_append($stream, $specificUnfilterName, STREAM_FILTER_READ, [
                'key' => $key,
                'iv' => $iv
            ]);

            $body = $this->getStreamFactory()->createStreamFromResource($stream);
            $response = $this
                ->createResponse()
                ->withHeader('Content-Length', $length)
                ->withBody($body);

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);

            $this->expectOutputString('Hello');
        } else {
            $this->assertTrue(true);
        }
    }

    public function testRespondIndeterminateLength()
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, 'Hello');
        rewind($stream);

        $body = $this
            ->getMockBuilder(MockStream::class)
            ->setConstructorArgs([$stream])
            ->setMethods(['getSize'])
            ->getMock();
        $body->method('getSize')->willReturn(null);

        $response = $this->createResponse()->withBody($body);
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);

        $this->expectOutputString('Hello');
    }

    public function testResponseWithStreamReadYieldingLessBytesThanAsked()
    {
        $body = new SmallChunksStream();
        $response = $this->createResponse()->withBody($body);

        $responseEmitter = new ResponseEmitter($body::CHUNK_SIZE * 2);
        $responseEmitter->emit($response);

        $this->expectOutputString(str_repeat('.', $body->getSize()));
    }

    public function testResponseReplacesPreviouslySetHeaders()
    {
        $response = $this
            ->createResponse(200, 'OK')
            ->withHeader('X-Foo', 'baz1')
            ->withAddedHeader('X-Foo', 'baz2');
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);

        $expectedStack = [
            ['header' => 'X-Foo: baz1', 'replace' => true, 'status_code' => null],
            ['header' => 'X-Foo: baz2', 'replace' => false, 'status_code' => null],
            ['header' => 'HTTP/1.1 200 OK', 'replace' => true, 'status_code' => 200],
        ];

        $this->assertSame($expectedStack, HeaderStack::stack());
    }

    public function testResponseDoesNotReplacePreviouslySetSetCookieHeaders()
    {
        $response = $this
            ->createResponse(200, 'OK')
            ->withHeader('Set-Cookie', 'foo=bar')
            ->withAddedHeader('Set-Cookie', 'bar=baz');
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);

        $expectedStack = [
            ['header' => 'Set-Cookie: foo=bar', 'replace' => false, 'status_code' => null],
            ['header' => 'Set-Cookie: bar=baz', 'replace' => false, 'status_code' => null],
            ['header' => 'HTTP/1.1 200 OK', 'replace' => true, 'status_code' => 200],
        ];

        $this->assertSame($expectedStack, HeaderStack::stack());
    }

    public function testIsResponseEmptyWithNonEmptyBodyAndTriggeringStatusCode()
    {
        $body = $this->createStream('Hello');
        $response = $this
            ->createResponse(204)
            ->withBody($body);
        $responseEmitter = new ResponseEmitter();

        $this->assertTrue($responseEmitter->isResponseEmpty($response));
    }

    public function testIsResponseEmptyWithEmptyBody()
    {
        $response = $this->createResponse(200);
        $responseEmitter = new ResponseEmitter();

        $this->assertTrue($responseEmitter->isResponseEmpty($response));
    }
}
