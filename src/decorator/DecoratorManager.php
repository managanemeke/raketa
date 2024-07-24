<?php

namespace src\decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    public CacheItemInterface $cacheItem;
    public LoggerInterface $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param array $request
     * @param LoggerInterface $logger
     * @param CacheItemInterface $cacheItem
     */
    public function __construct(
        string $host,
        string $user,
        string $password,
        array $request,
        LoggerInterface $logger,
        CacheItemInterface $cacheItem
    ) {
        parent::__construct($host, $user, $password, $request);
        $this->logger = $logger;
        $this->cacheItem = $cacheItem;
    }

    public function getResponse(): array
    {
        return $this->getAndCacheAndLogResponse();
    }

    public function getAndCacheAndLogResponse(): array
    {
        try {
            return $this->getAndCacheResponse();
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    public function getAndCacheResponse(): array
    {
        $cacheItem = $this->cacheItem;
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = parent::getResponse();

        $this->storeResponse($result);

        return $result;
    }

    private function storeResponse(array $result): void
    {
        $this->cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );
    }
}
