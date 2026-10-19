<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
  public function handle(Request $request, Closure $next): Response
{
    // Bypass total temporal para evitar bloqueos
    return $next($request);
}
}