<?php

namespace Sonysarkis\Skills\Facades;

use Illuminate\Support\Facades\Facade;

class Skills extends Facade
{
   
    protected static function getFacadeAccessor(): string
    {
        return 'skills';
    }
}