<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BotSecretMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.bot.secret');

        if (!$secret || $request->header('X-Bot-Secret') !== $secret) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
