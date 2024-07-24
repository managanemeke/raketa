<?php

namespace src\integration;

class Response
{
    private Credential $credential;
    private array $request;

    /**
     * @param Credential $credential
     * @param array $request
     */
    public function __construct(
        Credential $credential,
        array $request
    ) {
        $this->credential = $credential;
        $this->request = $request;
    }

    public function result(): array
    {
        // returns a response from external service
        return [];
    }
}