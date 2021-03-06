<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        // \RenatoMarinho\LaravelPageSpeed\Middleware\CollapseWhitespace::class
    ];
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],
        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'install' => \App\Http\Middleware\Install::class,
        'canInstall' => \RachidLaasri\LaravelInstaller\Middleware\canInstall::class,
        'locale' => \App\Http\Middleware\Locale::class,
        'api_locale' => \App\Http\Middleware\ApiLocale::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'disable_user' => \App\Http\Middleware\DisableUser::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'session_check' => \App\Http\Middleware\SessionCheck::class,
        'protection' => \App\Http\Middleware\XSSProtection::class,
        'url_decode' => \App\Http\Middleware\URLDecode::class,
        'admin_can' => \App\Http\Middleware\EntrustPermission::class,
        'manage_listing_auth' => \App\Http\Middleware\ManageListingAuth::class,
        'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
