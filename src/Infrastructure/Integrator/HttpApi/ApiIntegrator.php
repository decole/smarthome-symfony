<?php

namespace App\Infrastructure\Integrator\HttpApi;

use GuzzleHttp\Client;
use Throwable;

final class ApiIntegrator
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 60,
        ]);
    }

    public function get(string $uri, array $params = []): mixed
    {
        try {
            $response = $this->client->get($uri, [
                'query' => $params,
            ]);

            return $response->getBody()->getContents() ?? null;
        } catch (Throwable $exception) {
            return null;
        }
    }

    public function post(string $uri, array $params = []): mixed
    {
        try {
            $response = $this->client->post($uri, [
                'query' => $params,
            ]);

            return $response->getBody()->getContents() ?? null;
        } catch (Throwable $exception) {
            return null;
        }
    }
}