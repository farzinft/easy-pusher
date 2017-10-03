<?php

namespace Farzin\Pusher;

use Illuminate\Support\Facades\Facade;

class FarzinPusherFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new NotificationSender;
    }
}