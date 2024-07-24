<?php

namespace src\integration;

class DataProvider
{
    private $host;
    private $user;
    private $password;
    private array $request;

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param array $request
     */
    public function __construct($host, $user, $password, array $request)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function get()
    {
        // returns a response from external service
    }
}