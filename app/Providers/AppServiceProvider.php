<?php

namespace App\Providers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $pendingApprovalCount = 0;
            try {
                if (Auth::check()) {
                    $pendingApprovalCount = Report::pendingApprovalFor(Auth::user())->count();
                }
            } catch (\Throwable $e) {
                $pendingApprovalCount = 0;
            }
            $view->with('pendingApprovalCount', $pendingApprovalCount);
        });
    }
}
