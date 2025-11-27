<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Session;
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
        //Redirect an Authenticated User to dashboard
        RedirectIfAuthenticated::redirectUsing(function(){
            return route('admin.dashboard');
        });

        //Redirect No Authenticated User to Admin login page
        Authenticate::redirectUsing(function(){
            Session::flash('fail', 'You must be logged in to access admin area. Please login to continue.');
            return route('admin.login');
        });
    }
}
