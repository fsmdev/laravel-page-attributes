<?php

namespace Fsmdev\LaravelPageAttributes\Facades;

use Illuminate\Support\Facades\Facade;

class PageAttributes extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'fsmdev_laravel_page_attributes';
    }
}