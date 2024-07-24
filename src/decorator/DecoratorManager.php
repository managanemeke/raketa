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
     * @param LoggerInterface $logger
     * @param CacheItemInterface $cacheItem
     */
    public function __construct($host, $user, $password, LoggerInterface $logger, CacheItemInterface $cacheItem)
    {
        parent::__construct($host, $user, $password);
        $this->logger = $logger;
        $this->cacheItem = $cacheItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    {
        try {
            return $this->getAndCacheResponse($input);
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    public function getAndCacheResponse(array $input): array
    {
        $cacheItem = $this->cacheItem;
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = parent::get($input);

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
