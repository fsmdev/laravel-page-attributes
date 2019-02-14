<?php

namespace Fsmdev\LaravelPageAttributes;

use Fsmdev\LaravelPageAttributes\Models\PageAttributes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PageAttributesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::singleton('fsmdev_laravel_page_attributes', function() {
            return new PageAttributes();
        });

        $this->mergeConfigFrom(
            __DIR__.DIRECTORY_SEPARATOR.'config/page_attributes.php', 'page_attributes'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bladeDirectives();

        $this->loadViewsFrom(__DIR__.DIRECTORY_SEPARATOR.'views', 'page_attributes');

        $this->loadMigrationsFrom(__DIR__.DIRECTORY_SEPARATOR.'migrations');

        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'views' => resource_path('views/vendor/page_attributes'),
        ], 'views');

        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'config/page_attributes.php' => config_path('page_attributes.php'),
        ], 'config');

        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'ConstantsCollections/PageAttributesContext.stub'
            => app_path('ConstantsCollections/PageAttributesContext.php'),
        ], 'constants');
    }

    /**
     * Init Blade directives
     */
    protected function bladeDirectives()
    {
        Blade::directive('title', function () {
            return '<?= PageAttributes::html("title") ?>';
        });

        Blade::directive('description', function () {
            return '<?= PageAttributes::html("description") ?>';
        });

        Blade::directive('keywords', function () {
            return '<?= PageAttributes::html("keywords") ?>';
        });

        Blade::directive('h1', function () {
            return '<?= PageAttributes::html("h1") ?>';
        });

        Blade::directive('charset', function () {
            return '<?= PageAttributes::html("charset") ?>';
        });

        Blade::directive('viewport', function () {
            return '<?= PageAttributes::html("viewport") ?>';
        });

        Blade::directive('canonical', function () {
            return '<?= PageAttributes::html("canonical") ?>';
        });
    }
}
