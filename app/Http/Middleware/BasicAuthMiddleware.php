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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $USERNAME = 'admin';
        $PASSWORD = 'u7Z6il2kT$';

        $user = $request->getUser();
        $pass = $request->getPassword();

        if ($user !== $USERNAME || $pass !== $PASSWORD) {
            return response('Zaloguj się żeby korzystać ze strony', 401, [
                'WWW-Authenticate' => 'Basic realm="Apilo Access"',
            ]);
        }

        return $next($request);
    }
}
