<?php

namespace App\Services\Apilo;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;

class ApiloClient
{
    protected string $baseUrl;

    protected ApiloAuthService $authService;

    public function __construct(ApiloAuthService $authService)
    {
        $this->baseUrl = rtrim(config('apilo.base_url'), '/');
        $this->authService = $authService;
    }

    protected function getAccessToken(): string
    {
        return $this->authService->getAccessToken();
    }

    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function get(string $uri, array $query = []): ClientResponse
    {
        return Http::withHeaders($this->headers())
            ->get($this->url($uri), $query);
    }

    public function post(string $uri, array $payload): ClientResponse
    {
        return Http::withHeaders($this->headers())
            ->post($this->url($uri), $payload);
    }

    public function put(string $uri, array $payload): ClientResponse
    {
        return Http::withHeaders($this->headers())
            ->put($this->url($uri), $payload);
    }

    private function url(string $uri): string
    {
        return $this->baseUrl . '/' . ltrim($uri, '/');
    }
}
