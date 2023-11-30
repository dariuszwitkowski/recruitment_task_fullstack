<?php

namespace App\Service;

interface ExchangeRatesClientInterface
{
    public function fetchApi(string $date): array;
}