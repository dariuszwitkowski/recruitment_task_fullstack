<?php

namespace App\Service;

use App\Helper\MathHelper;
use Symfony\Component\Yaml\Yaml;

class NbpExchangeRatesService implements ExchangeRatesInterface
{
    /** @var array */
    private $currencyConfig;

    /** @var ExchangeRatesClientInterface */
    private $clientService;

    public function __construct(
        ExchangeRatesClientInterface $clientService,
        string $currencyConfig
    ) {
        $this->clientService = $clientService;
        $this->currencyConfig = Yaml::parse(file_get_contents($currencyConfig))['currency_config'];
    }

    public function getExchangeRates(string $date): array
    {
        $exchangeRates = $this->clientService->fetchApi($date);
        $response = [];
        if ($exchangeRates !== []) {
            $response['rates'] = $this->applyRateDifferences(
                $this->filterCurrencies($exchangeRates)
            );
        }

        $response['currencies'] = $this->getAvailableCurrencies();
        return $response;
    }

    private function filterCurrencies(array $exchangeRates): array
    {
        return array_values(
            array_filter($exchangeRates, function ($rate) {
                return array_key_exists($rate['code'], $this->currencyConfig);
            })
        );
    }

    private function applyRateDifferences(array $exchangeRates): array
    {
        return array_map(function (array $rate) {
            $currencyCode = $rate['code'];
            $currentCurrency = $this->currencyConfig[$currencyCode];

            if (isset($currentCurrency['buy_diff'])) {
                $rate['buy'] = MathHelper::sumFloats($rate['mid'], $currentCurrency['buy_diff']);
            }
            $rate['sell'] = MathHelper::sumFloats($rate['mid'], $currentCurrency['sell_diff']);
            unset($rate['mid'], $rate['currency']);

            return $rate;
        }, $exchangeRates);
    }

    private function getAvailableCurrencies(): array
    {
        return array_map(function ($currency) {
            return $currency['name'];
        }, $this->currencyConfig);
    }
}