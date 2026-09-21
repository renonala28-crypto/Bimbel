<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SyncSessionUser
{
    public function handle(Request $request, Closure $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (auth()->check()) {
            $user = auth()->user();
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_nickname'] = $user->nickname;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_status'] = $user->status;
        }

        return $next($request);
    }
}
