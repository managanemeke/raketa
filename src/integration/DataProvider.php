<?php

namespace src\integration;

class DataProvider
{
    private string $host;
    private string $user;
    private string $password;
    private array $request;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param array $request
     */
    public function __construct(
        string $host,
        string $user,
        string $password,
        array $request
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->request = $request;
    }

    public function getResponse(): array
    {
        // returns a response from external service
        return [];
    }
}