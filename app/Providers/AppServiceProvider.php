<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Menu;
use Illuminate\Support\Facades\View;

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
        //
            View::composer('elements.sidebar', function ($view) {
            // Ambil menu tingkat atas (parent = 0) beserta anak-anaknya
            $menus = Menu::with('children')
                ->where('parent', 0)
                ->where('aktif', 1)
                ->orderBy('urut', 'asc')
                ->get();

            $view->with('menus', $menus);
        });
    }
}
