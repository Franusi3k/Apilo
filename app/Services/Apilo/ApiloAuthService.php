<?php

namespace App\Services\Apilo;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ApiloAuthService
{
    private string $tokenFile = 'apilo_tokens.json';

    private int $expireMargin = 60;

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

        $data = $this->getResponse($payload);

        $tokens = $this->makeTokenArray($data);

        $this->saveTokens($tokens);

        return $tokens;
    }

    private function loadTokens(): array
    {
        if (! Storage::disk('local')->exists($this->tokenFile)) {
            throw new Exception('Brak pliku tokens.json!');
        }

        return json_decode(Storage::disk('local')->get($this->tokenFile), true);
    }

    private function saveTokens(array $tokens): void
    {
        Storage::disk('local')->put($this->tokenFile, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function refreshAccessToken(string $refreshToken): array
    {
        $payload = [
            'grantType' => 'refresh_token',
            'token' => trim($refreshToken),
        ];

        $data = $this->getResponse($payload);

        $tokens = $this->makeTokenArray($data, $refreshToken);

        $this->saveTokens($tokens);

        return $tokens;
    }

    private function calculateExpiresAt(?string $expiresAtStr): int
    {
        if ($expiresAtStr) {
            $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $expiresAtStr);
            $expiresIn = $dt->timestamp - time();
        } else {
            $expiresIn = 21 * 24 * 3600;
        }

        return time() + $expiresIn - $this->expireMargin;
    }

    private function makeTokenArray(array $data, ?string $fallbackRefreshToken = null): array
    {
        return [
            'access_token'  => $data['accessToken'],
            'refresh_token' => $data['refreshToken'] ?? $fallbackRefreshToken,
            'expires_at'    => $this->calculateExpiresAt($data['accessTokenExpireAt'] ?? null),
        ];
    }

    private function getResponse(array $payload): array
    {
        $response = Http::post(
            rtrim(config('apilo.base_url'), '/') . '/rest/auth/token/',
            $payload
        );

        if (! $response->successful()) {
            throw new Exception('Nie udało się utworzyć tokenów: ' . $response->json('message'));
        }

        return $response->json();
    }
}
