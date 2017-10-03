<?php

namespace Farzin\Pusher;

use Illuminate\Support\ServiceProvider;
use Vinkla\Pusher\PusherServiceProvider;

class FarzinPusherServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/farzin-pusher.php' => config_path('farzin-pusher.php'),
        ]);

        include __DIR__ . '/routes.php';
    }

    public function register()
    {
        $this->app->make(PusherController::class);

        $this->app->make(PusherServiceProvider::class);

        $this->app->singleton(NotificationSender::class, function ($app) {
            return new NotificationSender();
        });
    }

}