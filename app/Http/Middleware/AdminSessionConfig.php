<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionConfig
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(protected \Illuminate\Session\SessionManager $sessionManager)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLivewire = $request->is('livewire/*');
        $referer = $request->header('referer');
        $isAdminReferer = $referer && (str_contains(parse_url($referer, PHP_URL_PATH), '/admin') || str_starts_with(ltrim(parse_url($referer, PHP_URL_PATH), '/'), 'admin'));

        if ($request->is('admin*') || ($isLivewire && $isAdminReferer)) {
            $cookieName = 'admin_session';
            config(['session.cookie' => $cookieName]);
            $this->sessionManager->setName($cookieName);
        }

        return $next($request);
    }
}
