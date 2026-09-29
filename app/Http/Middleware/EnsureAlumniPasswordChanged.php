<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAlumniPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->needsForcedPasswordChange()) {
            // Allow:
            //   • the change-password page itself
            //   • all Livewire internal requests (so the form + logout can work)
            if (
                ! $request->routeIs('password.change') &&
                ! $request->hasHeader('X-Livewire')
            ) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}