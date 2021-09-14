<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyBot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('test', $request->all());
        if ($request->input("hub_mode") === "subscribe"
            && $request->input("hub_verify_token") === env("MESSENGER_VERIFY_TOKEN")) {
            return response($request->input("hub_challenge"), 200);
        }
        return $next($request);
    }
}
