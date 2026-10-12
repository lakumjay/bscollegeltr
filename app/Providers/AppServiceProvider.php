<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Profile;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // The admin uses Bootstrap. Without this Laravel emits Tailwind markup
        // whose responsive SVG controls render incorrectly in this project.
        Paginator::useBootstrap();

        // Share college profile with ALL views so logo/name/address is always available
        View::composer('*', function ($view) {
            try {
                $profile = cache()->remember('college_profile', 300, function () {
                    return Profile::first();
                });
            } catch (\Exception $e) {
                $profile = null;
            }
            $view->with('collegeProfile', $profile);
        });
    }
}
