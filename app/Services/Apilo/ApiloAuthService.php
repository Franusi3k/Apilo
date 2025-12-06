<?php

namespace App\Services\Apilo;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApiloAuthService
{
    protected string $baseUrl;

    protected string $clientId;

    protected string $clientSecret;

    protected string $tokenFile = 'apilo_tokens.json';

    protected int $expireMargin = 60;

    public function __construct()
    {
        $this->baseUrl = config('apilo.base_url');
        $this->clientId = config('apilo.client_id');
        $this->clientSecret = config('apilo.client_secret');
    }

    public function loadTokens(): array
    {
        if (! Storage::disk('local')->exists($this->tokenFile)) {
            throw new Exception('Brak pliku tokens.json!');
        }

        return json_decode(Storage::disk('local')->get($this->tokenFile), true);
    }

    public function saveTokens(array $tokens): void
    {
        Storage::disk('local')->put($this->tokenFile, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $payload = [
            'grantType' => 'refresh_token',
            'token' => trim($refreshToken),
        ];

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl . '/rest/auth/token/', $payload);

        if (! $response->successful()) {
            throw new Exception('Nie udało się odświeżyć tokenu: ' . $response->body());
        }

        $data = $response->json();

        $expiresAtStr = $data['accessTokenExpireAt'] ?? null;
        if ($expiresAtStr) {
            $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $expiresAtStr);
            $expiresIn = $dt->timestamp - time();
        } else {
            $expiresIn = 21 * 24 * 3600;
        }

        $tokens = [
            'access_token' => $data['accessToken'],
            'refresh_token' => $data['refreshToken'] ?? $refreshToken,
            'expires_at' => time() + $expiresIn - $this->expireMargin,
        ];

        $this->saveTokens($tokens);

        return $tokens;
    }

    public function getAccessToken(): string
    {
        $tokens = $this->loadTokens();

        if (time() > ($tokens['expires_at'] ?? 0)) {
            $tokens = $this->refreshAccessToken($tokens['refresh_token']);
        }

        return $tokens['access_token'];
    }

    public function exchangeAuthorizationCode(string $code): array
    {
        $payload =  [
            "grantType" => "authorization_code",
            "token" => $code,
        ];

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->acceptJson()
            ->post($this->baseUrl . '/rest/auth/token/', $payload);

        if (! $response->successful()) {
            throw new Exception('Nie udało się utworzyć tokenów: ' . $response->json('message'));
        }

        $data = $response->json();

        $expiresAtStr = $data['accessTokenExpireAt'] ?? null;
        if ($expiresAtStr) {
            $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $expiresAtStr);
            $expiresIn = $dt->timestamp - time();
        } else {
            $expiresIn = 21 * 24 * 3600;
        }

        $tokens = [
            'access_token' => $data['accessToken'],
            'refresh_token' => $data['refreshToken'],
            'expires_at' => time() + $expiresIn - $this->expireMargin,
        ];

        $this->saveTokens($tokens);

        return $tokens;
    }
}
