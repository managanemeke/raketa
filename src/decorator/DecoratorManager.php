<?php

namespace src\decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    public $cacheItem;
    public $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param array $request
     * @param LoggerInterface $logger
     * @param CacheItemInterface $cacheItem
     */
    public function __construct($host, $user, $password, array $request, LoggerInterface $logger, CacheItemInterface $cacheItem)
    {
        parent::__construct($host, $user, $password, $request);
        $this->logger = $logger;
        $this->cacheItem = $cacheItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $this->getAndCacheAndLogResponse();
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

        $result = parent::get();

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
