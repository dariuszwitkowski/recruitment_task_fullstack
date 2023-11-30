<?php

namespace App\Service;

interface ExchangeRatesInterface
{
    public function getExchangeRates(string $date): array;
}