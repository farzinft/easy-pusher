<?php
namespace Farzin\EasyPusher\Facade;

use Farzin\EasyPusher\EasyPusher;
use Illuminate\Support\Facades\Facade;

class EasyPusherFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new EasyPusher;
    }
}