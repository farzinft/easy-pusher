<?php

namespace EasyPusher;

use EasyPusher\Controller\EasyPusherController;
use Illuminate\Support\ServiceProvider;


class EasyPusherServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/easy-pusher.php' => config_path('easy-pusher.php'),
        ]);

        include __DIR__ . '/routes.php';

        include __DIR__ . '/helpers/helpers.php';
    }

    public function register()
    {
        $this->app->make(EasyPusherController::class);

        $this->app->singleton(EasyPusher::class, function ($app) {
            return new EasyPusher();
        });
    }

}