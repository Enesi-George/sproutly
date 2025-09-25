<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    // public function boot(): void
    // {
    //     $modules = ['Auth', 'User'];

    //     foreach ($modules as $module) {
    //         $routesPath = base_path("app/Modules/{$module}/routes.php");
    //         if (file_exists($routesPath)) {
    //             require $routesPath;
    //         }
    //     }
    // }

    // //auto detect modules
    public function boot()
    {
        $modulesPath = base_path('app/Modules');

        if (is_dir($modulesPath)) {
            $modules = scandir($modulesPath);

            foreach ($modules as $module) {
                if ($module === '.' || $module === '..') {
                    continue;
                }

                $routes = $modulesPath . "/{$module}/routes.php";

                if (file_exists($routes)) {
                    require $routes;
                }
            }
        }
    }
}
