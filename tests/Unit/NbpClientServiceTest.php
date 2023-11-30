<?php

declare(strict_types=1);

namespace Unit;

use App\Service\NbpClientService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class NbpClientServiceTest extends TestCase
{
    private $clientMock;
    private $cacheMock;
    private $loggerMock;
    private $nbpClientService;

    private const TEST_DATA = [
        [
            'rates' => [
                [
                    'currency' => 'dolar amerykański',
                    'code' => 'USD',
                    'mid' => 3.9478
                ],
                [
                    'currency' => 'euro',
                    'code' => 'EUR',
                    'mid' => 4.3327
                ],
                [
                    'currency' => 'korona czeska',
                    'code' => 'CZK',
                    'mid' => 0.1784
                ],
                [
                    'currency' => 'rupia indonezyjska',
                    'code' => 'IDR',
                    'mid' => 0.047382
                ],
                [
                    'currency' => 'real (Brazylia)',
                    'code' => 'BRL',
                    'mid' => 0.8104
                ],
            ]
        ],
    ];


    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(HttpClientInterface::class);
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->nbpClientService = new NbpClientService(
            $this->clientMock,
            $this->loggerMock,
            $this->cacheMock,
            'https://api.nbp.pl/api/',
            'exchangerates/tables/A/%s/?format=json'
        );
    }

    public function testFetchApiWithException()
    {
        $date = '2023-01-01';

        $this->clientMock->method('request')
            ->will($this->throwException(new \Exception("Error")));
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->equalTo("Error"));

        $cacheItemMock = $this->createMock(ItemInterface::class);

        $this->cacheMock->method('get')->willReturnCallback(function ($key, $callback) use ($cacheItemMock) {
            return $callback($cacheItemMock);
        });

        $result = $this->nbpClientService->fetchApi($date);
        $this->assertEquals([], $result);
    }

    public function testFetchApiWithValidResponseAndCache()
    {
        $date = '2023-01-01';

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')->willReturn(self::TEST_DATA);

        $cacheItemMock = $this->createMock(ItemInterface::class);
        $cacheItemMock->method('get')->willReturn(self::TEST_DATA[0]['rates']);
        $cacheItemMock->method('expiresAt')->willReturn($cacheItemMock);
        $cacheItemMock->method('isHit')->willReturn(true);

        $this->cacheMock->method('get')->willReturnCallback(function ($key, $callback) use ($cacheItemMock) {
            if ($cacheItemMock->isHit()) {
                return self::TEST_DATA[0]['rates'];
            }

            return $callback($cacheItemMock);
        });

        $result = $this->nbpClientService->fetchApi($date);

        $this->assertEquals(self::TEST_DATA[0]['rates'], $result);
    }

    public function testFetchApiWithValidResponseAndNoCache()
    {
        $date = '2023-01-01';

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')->willReturn(self::TEST_DATA);

        $cacheItemMock = $this->createMock(ItemInterface::class);
        $cacheItemMock->method('isHit')->willReturn(false);

        $this->cacheMock->method('get')->willReturnCallback(
            function ($key, $callback) use ($cacheItemMock, $responseMock) {
                return $callback($cacheItemMock, $responseMock);
            }
        );

        $this->clientMock->method('request')->willReturn($responseMock);

        $result = $this->nbpClientService->fetchApi($date);

        $this->assertEquals(self::TEST_DATA[0]['rates'], $result);
    }
}