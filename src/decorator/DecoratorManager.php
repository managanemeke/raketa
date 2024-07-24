<?php

namespace src\decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    public $cache;
    public $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param LoggerInterface $logger
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($host, $user, $password, LoggerInterface $logger, CacheItemPoolInterface $cache)
    {
        parent::__construct($host, $user, $password);
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    {
        try {
            return $this->cacheResponse($input);
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    public function cacheResponse(array $input): array
    {
        $cacheItem = $this->cacheItem($input);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = parent::get($input);

        $this->storeResponse($cacheItem, $result);

        return $result;
    }

    public function cacheItem(array $input): CacheItemInterface
    {
        $cacheKey = json_encode($input);
        return $this->cache->getItem($cacheKey);
    }

    private function storeResponse(CacheItemInterface $cacheItem, array $result): void
    {
        $cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );
    }
}
