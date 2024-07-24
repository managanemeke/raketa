<?php

namespace src\integration;

class Credential
{
    private string $host;
    private string $user;
    private string $password;

    public function __construct(
        string $host,
        string $user,
        string $password
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function password(): string
    {
        return $this->password;
    }
}
