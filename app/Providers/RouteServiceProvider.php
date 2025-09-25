<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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
    public function boot(): void
    {
        // add v1 prefix to all routes
        $this->app['router']->prefix('api/v1')->group(function () {
            $modulesPath = base_path('app/Modules');

            if (is_dir($modulesPath)) {
                $allModules = scandir($modulesPath);

                foreach ($allModules as $module) {
                    // skip . and ..
                    if ($module === '.' || $module === '..') {
                        continue;
                    }

                    $routesPath = $modulesPath . '/' . $module . '/routes.php';

                    if (file_exists($routesPath)) {
                        require $routesPath;
                    }
                }
            }
        });
    }
}
