<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests\Request;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Xabbuh\XApi\Client\Request\Handler;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class HandlerTest extends TestCase
{
    /**
     * @var ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestFactory;

    /**
     * @var RequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $request;

    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->handler = new Handler($this->httpClient, $this->requestFactory, 'http://example.com/xapi', '1.0.3');
    }

    public function testCreateRequestWithGetMethod()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest('GET', '/statements');

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithPostMethod()
    {
        $body = '{"id":"12345678-1234-5678-1234-567812345678"}';
        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'POST',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $this->request->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->once())
            ->method('write')
            ->with($body)
            ->willReturn(strlen($body));

        $stream->expects($this->once())
            ->method('rewind');

        $this->request->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($this->request);

        $request = $this->handler->createRequest('POST', '/statements', [], $body);

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithPutMethod()
    {
        $body = '{"id":"12345678-1234-5678-1234-567812345678"}';
        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'PUT',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $this->request->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->once())
            ->method('write')
            ->with($body)
            ->willReturn(strlen($body));

        $stream->expects($this->once())
            ->method('rewind');

        $this->request->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($this->request);

        $request = $this->handler->createRequest('PUT', '/statements', [], $body);

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithDeleteMethod()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'DELETE',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest('DELETE', '/statements');

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithUrlParameters()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://example.com/xapi/statements?statementId=12345678-1234-5678-1234-567812345678&limit=10'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest(
            'GET',
            '/statements',
            [
                'statementId' => '12345678-1234-5678-1234-567812345678',
                'limit' => 10,
            ]
        );

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithCustomHeaders()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(3))
            ->method('withHeader')
            ->withConsecutive(
                ['Authorization', 'Basic dXNlcm5hbWU6cGFzc3dvcmQ='],
                ['X-Experience-API-Version', '1.0.3'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest(
            'GET',
            '/statements',
            [],
            null,
            ['Authorization' => 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=']
        );

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithCustomApiVersion()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['X-Experience-API-Version', '1.0.2'],
                ['Content-Type', 'application/json']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest(
            'GET',
            '/statements',
            [],
            null,
            ['X-Experience-API-Version' => '1.0.2']
        );

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithCustomContentType()
    {
        $this->requestFactory->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://example.com/xapi/statements'
            )
            ->willReturn($this->request);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['Content-Type', 'application/xml'],
                ['X-Experience-API-Version', '1.0.3']
            )
            ->willReturnOnConsecutiveCalls(
                $this->request,
                $this->request
            );

        $request = $this->handler->createRequest(
            'GET',
            '/statements',
            [],
            null,
            ['Content-Type' => 'application/xml']
        );

        $this->assertSame($this->request, $request);
    }

    public function testCreateRequestWithInvalidMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '"PATCH" is no valid HTTP method (expected one of [GET, POST, PUT, DELETE]) in an xAPI context.'
        );

        $this->handler->createRequest('PATCH', '/statements');
    }
}
