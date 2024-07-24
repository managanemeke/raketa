<?php

namespace src\integration;

use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;

class ResponseCacheAndLog implements ResponseInterface
{
    private ResponseInterface $response;
    private CacheItemInterface $cacheItem;
    private LoggerInterface $logger;

    public function __construct(
        ResponseInterface $response,
        LoggerInterface $logger,
        CacheItemInterface $cacheItem
    ) {
        $this->response = $response;
        $this->logger = $logger;
        $this->cacheItem = $cacheItem;
    }

    public function result(): array
    {
        return $this->retrieveAndCacheAndLogResult();
    }

    public function retrieveAndCacheAndLogResult(): array
    {
        try {
            return $this->retrieveAndCacheResult();
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    public function retrieveAndCacheResult(): array
    {
        $cacheItem = $this->cacheItem;
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->response->result();

        $this->storeResult($result);

        return $result;
    }

    private function storeResult(array $result): void
    {
        $this->cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );
    }
}
