<?php

namespace src\integration;

use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;

class ResponseCacheAndLog extends Response implements ResponseInterface
{
    private CacheItemInterface $cacheItem;
    private LoggerInterface $logger;

    public function __construct(
        Credential $credential,
        array $request,
        LoggerInterface $logger,
        CacheItemInterface $cacheItem
    ) {
        parent::__construct($credential, $request);
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

        $result = parent::result();

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
