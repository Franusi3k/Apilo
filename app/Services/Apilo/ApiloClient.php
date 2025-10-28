<?php

namespace App\Services\Apilo;

use Illuminate\Support\Facades\Http;

class ApiloClient
{
    protected string $baseUrl;

    protected ApiloAuthService $authService;

    public function __construct(ApiloAuthService $authService)
    {
        $this->baseUrl = config('apilo.base_url');
        $this->authService = $authService;
    }

    protected function getAccessToken(): string
    {
        return $this->authService->getAccessToken();
    }

    /**
     * Returns headers for all Apilo API requests
     */
    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->getAccessToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function testConnection(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get(config('apilo.base_url').'/rest/api');

        return [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ];
    }
}
