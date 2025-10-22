<?php

namespace App\Services\Apilo;

use Illuminate\Support\Facades\Http;

class ApiloClient
{
    protected string $token;

    public function __construct()
    {
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NjA2ODkyODUsImlzcyI6InNrbGVwYWRnby5hcGlsby5jb20iLCJleHAiOjE3NjI1MDcyODUsImp0aSI6IjVkZDlkMjczLWJlODMtNTgwNy04OTIyLTYxMDI1ZjMxMGE3YyIsInR5cGUiOiJhY2Nlc3NfdG9rZW4iLCJjbGllbnRfaWQiOjMsInBsYXRmb3JtX2lkIjo4NH0.Izuc5BHrBgumiVW9IBBOnIoEs_Qj0sTA3cwP5kMS6Xq6oVaFWI_iKs8ld5Zi7VYr4lqsQQ-yiE1U2AhjjQ6d203_Y7aI6LJIMNh69ZbIAdNwp2fxPR-aspR7sGvDUSn35aMF2yBCoWlHDM1FIXTVdSwq1ZX5fJ4fx-gNqrfQjAhP6ifldKSBINFP8LRkHqRkacJit2o_AHfTwg6DcW8Vq2GVFFB50qPaCaFfvjsbKirpUFrLZBTz-81Lqd5Mb9RiOIsbFqM5P3FD69HzJ3NQpCORYSc6jBRh9iI1e4YpUgfPfBHFlRaCL-U6ocf6z6FcD7PIR4-1_TOTysBeB0TRSV55aryeIEq2K5VlajrYkV5fObO3pHXWEOqUxY99h6mat--galxoRIdpA_qqvsvzn1Skt8AIcfo7D3OxfJw5NbvkIVIC98P-0FtxSNbwxeH05uMkejLMo6CkHj9cL-Ij7Vst_eKpMjN-YbzT6z7Sm9yI4Cibf2LUp9fcM7CtEbMwAwgz53iM4FEUjhYBDFrR0eMeJymHewqVlLDfDJbZy54zkplM-Z209XOXnp9mYXPCYTmq9yksEHb8ujEJ7Y5epKmF79xojlxhhzeHqQcX-a03rEywoabxCSwhee4AUbE72cqtf-ycXRcwUymIIVVvAViy6O4mNon5WMmVEdkbL4w';
    }

    /**
     * Zwraca nagłówki do wszystkich żądań API Apilo
     */
    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function testConnection(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get(config('apilo.base_url'));

        return [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ];
    }
}
