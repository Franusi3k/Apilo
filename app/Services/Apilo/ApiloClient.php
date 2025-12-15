<?php

namespace App\Services\Apilo;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiloClient
{
    private readonly string $baseUrl;

    public function __construct(private ApiloAuthService $authService) 
    {
        $this->baseUrl = rtrim(config('apilo.base_url'), '/');
    }

    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function get(string $uri, array $query = []): Response
    {
        return Http::withHeaders($this->headers())
            ->get($this->url($uri), $query);
    }

    public function post(string $uri, array $payload): Response
    {
        return Http::withHeaders($this->headers())
            ->post($this->url($uri), $payload);
    }

    public function put(string $uri, array $payload): Response
    {
        return Http::withHeaders($this->headers())
            ->put($this->url($uri), $payload);
    }

    private function url(string $uri): string
    {
        return $this->baseUrl . '/' . ltrim($uri, '/');
    }
}
