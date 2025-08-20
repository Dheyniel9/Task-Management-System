<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * This middleware checks if the user is an admin
     * before letting them continue.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is NOT logged in or NOT an admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {

            // If the request comes from an API, send back a JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // If the request comes from the website, redirect them
            // back to their personal task list with an error message
            return redirect()->route('tasks.index')
                ->with('error', 'Admin access required.');
        }

        // If user is an admin, let them continue
        return $next($request);
    }
}
