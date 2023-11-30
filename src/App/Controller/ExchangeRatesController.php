<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ExchangeRatesInterface;
use App\Service\NbpExchangeRatesService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRatesController extends AbstractController
{
    /** @var NbpExchangeRatesService */
    private $exchangeRatesService;

    public function __construct(ExchangeRatesInterface $exchangeRatesService)
    {
        $this->exchangeRatesService = $exchangeRatesService;
    }

    public function getExchangeRates(Request $request): JsonResponse
    {
        return $this->json(
            $this->exchangeRatesService->getExchangeRates(
                $request->query->get('date', (new DateTime())->format('Y-m-d'))
            )
        );
    }
}
