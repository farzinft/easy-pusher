<?php
namespace EasyPusher\Facade;

use EasyPusher\EasyPusher;
use Illuminate\Support\Facades\Facade;

class EasyPusherFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new EasyPusher;
    }
}