<?php

namespace App\Services\Apilo;

use Illuminate\Support\Facades\Http;

class ApiloClient
{
    protected string $token;

    public function __construct()
    {
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NTk0Nzk2ODUsImlzcyI6InNrbGVwYWRnby5hcGlsby5jb20iLCJleHAiOjE3NjEyOTQwODUsImp0aSI6ImY4OWYxMzY5LTk1YWQtNTRkYy05MzU3LTIwOTFlNjk5OWZkNiIsInR5cGUiOiJhY2Nlc3NfdG9rZW4iLCJjbGllbnRfaWQiOjMsInBsYXRmb3JtX2lkIjo4NH0.gSWuaDskOtYrMefVhy8fGfGlAwsNlgkdYv0umlWHAC3SWs7v58U35lrqwJjHGGOAOfj9qigfcIW8xF41TD9akS2sh-y9Noa5imtntz-X7Ztop6Bg_rXulWUnLO6JA2ClM75bqsXJdQzWOMCSl5qmUgei67HdokJHgaTT39XOG_yuui6HF2GFBmsHGxZ7G1WX6q3Fwm27raxK5orUNcoUy8MurSr87jti9f5YvuAYjaZQ1qDn9_JzUhBnNd36AJCnSvhTvH1BI4dvu-IiC-JGoPwhABfFpFId25rKX9nVaPKcBZQqTmYIq0EuSf4QmRvct2yjyIZVKmjler6iRaqp8ZOl5czUTDor8hWeXYZ5VdDALjRlwlInrk5pzLnnHUL3_X_D6csBzBi3xrRYFAQYtnkNSC56DYwjeybvJk47Hc5TEWAZrjTkmgR8nUrt8euY99-gRO1_oZq_LaVgLmhA9eNtTgwgIRsjes0rrg2mYmHCQsrikFH4SDPX3y3cK6Va5GMk13t7Jg_80oRiXODhFH6TmvryJEAaDmx82k8j8oJ2r8kaXccpztn3SM9YT6pKhNovrFrYO0XSD-wnIoAAiPdEzYZMlg-LKE5YF7a3AA4rbaekTqXksMHdzsbclayg7YdlY2XnRvr7LUTUcyh8KmK-Fca4jJaiYpUtRosQelc';
    }

    /**
     * Zwraca nagłówki do wszystkich żądań API Apilo
     */
    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
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
