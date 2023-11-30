<?php

namespace App\Service;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpClientService implements ExchangeRatesClientInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var string */
    private $nbpApiUrl;

    /** @var string */
    private $nbpExchangeRatesRoute;

    /** @var LoggerInterface */
    private $logger;

    /** @var CacheInterface */
    private $cache;

    public function __construct(
        HttpClientInterface $client,
        LoggerInterface $logger,
        CacheInterface $cache,
        string $nbpApiUrl,
        string $nbpExchangeRatesRoute
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->nbpApiUrl = $nbpApiUrl;
        $this->nbpExchangeRatesRoute = $nbpExchangeRatesRoute;
    }

    public function fetchApi(string $date): array
    {
        try {
            $url = $this->nbpApiUrl . sprintf($this->nbpExchangeRatesRoute, $date);
            return $this->cache->get(md5($url), function (ItemInterface $item) use ($url) {
                $item->expiresAt((new \DateTime())->modify('+1 day'));

                return $this->client->request(Request::METHOD_GET, $url)->toArray()[0]['rates'];
            });
        } catch (Exception | InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}