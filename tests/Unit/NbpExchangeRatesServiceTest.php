<?php

declare(strict_types=1);

namespace Unit;

use App\Service\ExchangeRatesClientInterface;
use App\Service\NbpExchangeRatesService;
use PHPUnit\Framework\TestCase;

class NbpExchangeRatesServiceTest extends TestCase
{
    private $clientServiceMock;
    private $nbpExchangeRatesService;

    protected function setUp(): void
    {
        $this->clientServiceMock = $this->createMock(ExchangeRatesClientInterface::class);
        $this->nbpExchangeRatesService = new NbpExchangeRatesService(
            $this->clientServiceMock,
            'config/currency_config.yaml'
        );
    }

    public function testGetExchangeRatesSuccess()
    {
        $sampleDate = '2023-01-01';
        $sampleApiResponse = [
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
        ];

        $this->clientServiceMock->method('fetchApi')
            ->with($sampleDate)
            ->willReturn($sampleApiResponse);

        $result = $this->nbpExchangeRatesService->getExchangeRates($sampleDate);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('rates', $result);
        $this->assertArrayHasKey('currencies', $result);
        $this->assertArrayHasKey('EUR', $result['currencies']);
        $this->assertArrayHasKey('USD', $result['currencies']);
        $this->assertArrayHasKey('CZK', $result['currencies']);
        $this->assertArrayHasKey('IDR', $result['currencies']);
        $this->assertArrayHasKey('BRL', $result['currencies']);
    }

    public function testGetExchangeRatesNoDataFromApi()
    {
        $sampleDate = '2023-01-01';
        $sampleApiResponse = [];
        $this->clientServiceMock->method('fetchApi')
            ->with($sampleDate)
            ->willReturn($sampleApiResponse);

        $result = $this->nbpExchangeRatesService->getExchangeRates($sampleDate);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('rates', $result);
        $this->assertArrayHasKey('currencies', $result);
        $this->assertArrayHasKey('EUR', $result['currencies']);
        $this->assertArrayHasKey('USD', $result['currencies']);
        $this->assertArrayHasKey('CZK', $result['currencies']);
        $this->assertArrayHasKey('IDR', $result['currencies']);
        $this->assertArrayHasKey('BRL', $result['currencies']);
    }

}