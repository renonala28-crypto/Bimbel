<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveStatusMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isPending()) {
            return redirect()->route('pending');
        }

        if ($user->isRejected()) {
            return redirect()->route('rejected');
        }

        return $next($request);
    }
}
