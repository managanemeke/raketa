<?php

namespace src\decorator;

use DateTime;
use Exception;
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
        $cacheKey = $this->getCacheKey($input);
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = parent::get($input);

        $cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );

        return $result;
    }

    public function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}
