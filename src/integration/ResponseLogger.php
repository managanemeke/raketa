<?php

namespace src\integration;

use Exception;
use Psr\Log\LoggerInterface;

class ResponseLogger implements ResponseInterface
{
    private ResponseInterface $response;
    private LoggerInterface $logger;

    public function __construct(
        ResponseInterface $response,
        LoggerInterface $logger,
    ) {
        $this->response = $response;
        $this->logger = $logger;
    }

    public function result(): array
    {
        try {
            return $this->response->result();
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }
}
