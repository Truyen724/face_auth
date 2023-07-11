<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class CheckHeader
{
    public function handle($request, Closure $next)
    {
          if($request->headers('X-CLIENT-REQUEST'))
          {
            $lang = $request->header('x-client-language');
            App::setLocale($lang);
          }
          return $next($request);
    }
}
