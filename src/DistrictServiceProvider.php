<?php

namespace ChuJC\AMapDistrict;

use ChuJC\AMapDistrict\Console\DistrictCommand;
use ChuJC\AMapDistrict\Console\DistrictTableCommand;
use Illuminate\Support\ServiceProvider;

class DistrictServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('command.district.table',
            function ($app) {
                return new DistrictTableCommand($app['files'], $app['composer'], $app['migrator']);
            }
        );

        $this->app->singleton('command.district',
            function ($app) {
                return new DistrictCommand();
            }
        );

        $this->publishes([__DIR__.'/amap-district.php' => config_path('district.php')], 'config');

        $this->commands('command.district');
        $this->commands('command.district.table');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
