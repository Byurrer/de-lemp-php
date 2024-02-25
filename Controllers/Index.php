<?php

namespace App\Controllers;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Index
{
    public function __construct()
    {}

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function action(RequestInterface $request): ResponseInterface
    {
        return $response = new Response(200, [], file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/static/login.html'));
    }
}
