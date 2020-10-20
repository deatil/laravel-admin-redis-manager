<?php

namespace Lake\Admin\RedisManager\Traits;

use Illuminate\Support\Facades\Artisan;

use Encore\Admin\Admin;

trait BootExtension
{
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('lake-redis-manager', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->namespace('\\Lake\\Admin\\RedisManager\\Controller')->group(function ($router) {
                $router->get('lake-redis', 'Redis@index')->name('lake-redis-index');
                $router->delete('lake-redis/key', 'Redis@destroy')->name('lake-redis-key-delete');
                $router->get('lake-redis/fetch', 'Redis@fetch')->name('lake-redis-fetch-key');
                $router->get('lake-redis/create', 'Redis@create')->name('lake-redis-create-key');
                $router->post('lake-redis/store', 'Redis@store')->name('lake-redis-store-key');
                $router->get('lake-redis/edit', 'Redis@edit')->name('lake-redis-edit-key');
                $router->put('lake-redis/key', 'Redis@update')->name('lake-redis-update-key');
                $router->delete('lake-redis/item', 'Redis@remove')->name('lake-redis-remove-item');
                $router->put('lake-redis/rename', 'Redis@rename')->name('lake-redis-rename');

                $router->get('lake-redis/console', 'Redis@console')->name('lake-redis-console');
                $router->post('lake-redis/console', 'Redis@execute')->name('lake-redis-execute');
                
                $router->get('lake-redis/tool/zset-hot', 'Redis@zsethot')->name('lake-redis-tool-zset-hot');
                $router->get('lake-redis/tool/set-data', 'Redis@setdata')->name('lake-redis-tool-set-data');
                $router->post('lake-redis/tool/set-data', 'Redis@setdataStore')->name('lake-redis-tool-set-data-store');
                
                $router->resource('lake-redis/connection', 'Connection')->names('lake-redis-connection');
            });
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        Artisan::call('lake-redis-manager:install');
        
        parent::createMenu('Redis manager', 'lake-redis', 'fa-database');

        parent::createPermission('Redis Manager', 'ext.lake-redis', 'lake-redis*');
    }
}
