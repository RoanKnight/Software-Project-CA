<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, ...$roles)
  {
    // Check if the user is authenticated and has the required role
    if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
      // Redirect to the home page if the user is not authenticated or does not have the required role
      return redirect('/');
    }

    // Continue processing the request if the user is authenticated and has the required role
    return $next($request);
  }
}
