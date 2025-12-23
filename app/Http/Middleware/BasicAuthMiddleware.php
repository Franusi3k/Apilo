<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $USERNAME = 'admin';
        $PASSWORD = 'ceb5ca45c03499706aee2cfa28b239a1d497e6ff760c0283859add7d4514824c';

        $user = $request->getUser();
        $pass = $request->getPassword();

        if ($user !== $USERNAME || ! hash_equals($PASSWORD, hash('sha256', $pass))) {
            return response('Zaloguj się żeby korzystać ze strony', 401, [
                'WWW-Authenticate' => 'Basic realm="Apilo Access"',
            ]);
        }

        return $next($request);
    }
}
