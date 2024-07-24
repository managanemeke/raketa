<?php

namespace src\integration;

use DateTime;
use Psr\Cache\CacheItemInterface;

class ResponseCache implements ResponseInterface
{
    private ResponseInterface $response;
    private CacheItemInterface $cacheItem;

    public function __construct(
        ResponseInterface $response,
        CacheItemInterface $cacheItem
    ) {
        $this->response = $response;
        $this->cacheItem = $cacheItem;
    }

    public function result(): array
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
            ->expiresAt($this->tomorrow());
    }

    private function tomorrow(): DateTime
    {
        return (new DateTime())->modify('+1 day');
    }
}
