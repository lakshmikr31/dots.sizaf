<?php

namespace App\Providers;

use App\Models\UserWallpaper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallpaper;
use App\Models\SettingMenu;
use App\Models\ContextType;
use Illuminate\Support\Facades\Session;



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
    public function boot()
    {
        // Share wallpapers data with all views
        View::composer('*', function ($view) {
            $desktopWallpapers = Wallpaper::where('type', 0)
                ->where('status', 1)
                ->where('created_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            $loginWallpapers = Wallpaper::where('type', 1)
                ->where('status', 1)
                ->where('created_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            $userWallpaper = UserWallpaper::where('user_id', Auth::id())->first();
            // Default wallpaper
            $defaultWallpaper = 'Wallpaper.png';
            $user = $defaultWallpaper;
            $login = $defaultWallpaper;
            if ($userWallpaper) {
                $desktopWallpaper = Wallpaper::find($userWallpaper->dashboard_display);
                $loginWallpaper = Wallpaper::find($userWallpaper->login_display);

                // Set the image filename directly to the userWallpaper properties
                $userWallpaper->dashboard_display = $desktopWallpaper ? $desktopWallpaper->image : 'Wallpaper.png';
                $userWallpaper->login_display = $loginWallpaper ? $loginWallpaper->image : 'Wallpaper.png';
            }

            // Pass variables to view, no need for extra variables like $user and $login
            $view->with(compact('desktopWallpapers', 'loginWallpapers', 'userWallpaper'));
            
             View::composer('*', function ($view) {
                $menus = SettingMenu::where('status', 1)->get();
                $view->with('menus', $menus);
            });
            /// adding rightclick options  

                View::composer('layouts.filemanager-header', function ($view) {
                    $contexttypes = ContextType::with(['contextOptions' => function ($query) {
                        $query->orderBy('sort_order', 'asc'); // Sort options by sort_order
                    }])
                        ->where('display_header', 1)
                        ->whereIn('function', ['createFileFunction', 'resizeFunction', 'sortFunction']) // Fetch all functions in one query
                        ->orderBy('sort_order', 'asc') // Sort context types by sort_order
                        ->get();
        
                    // Separate the context types by function
                    $contextTypes = $contexttypes->where('function', 'createFileFunction');
                    $resizecontextTypes = $contexttypes->where('function', 'resizeFunction');
                    $sortcontextTypes = $contexttypes->where('function', 'sortFunction');
                    $view->with(compact( 'contextTypes', 'resizecontextTypes', 'sortcontextTypes'));
                });
                View::composer('*', function ($view) {
                    $is_list = Session::has('is_list') ? Session::get('is_list') : 0;
                    $sortorder = Session::has('sortorder') ? Session::get('sortorder') : 'asc';
                    $sortby = Session::has('sortby') ? Session::get('sortby') : 'id';
                    $iconsize = Session::has('iconsize') ? Session::get('iconsize') : 'medium';
                    $view->with(compact('is_list','sortorder','sortby','iconsize'));

                });
                
                
        });
    }
}
