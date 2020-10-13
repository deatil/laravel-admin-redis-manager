<?php

namespace Lake\Admin\RedisManager;

use Lake\Admin\Facades\Admin;

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
            $router->get('lake-redis', 'Lake\Admin\RedisManager\RedisController@index')->name('lake-redis-index');
            $router->delete('lake-redis/key', 'Lake\Admin\RedisManager\RedisController@destroy')->name('lake-redis-key-delete');
            $router->get('lake-redis/fetch', 'Lake\Admin\RedisManager\RedisController@fetch')->name('lake-redis-fetch-key');
            $router->get('lake-redis/create', 'Lake\Admin\RedisManager\RedisController@create')->name('lake-redis-create-key');
            $router->post('lake-redis/store', 'Lake\Admin\RedisManager\RedisController@store')->name('lake-redis-store-key');
            $router->get('lake-redis/edit', 'Lake\Admin\RedisManager\RedisController@edit')->name('lake-redis-edit-key');
            $router->put('lake-redis/key', 'Lake\Admin\RedisManager\RedisController@update')->name('lake-redis-update-key');
            $router->delete('lake-redis/item', 'Lake\Admin\RedisManager\RedisController@remove')->name('lake-redis-remove-item');

            $router->get('lake-redis/console', 'Lake\Admin\RedisManager\RedisController@console')->name('lake-redis-console');
            $router->post('lake-redis/console', 'Lake\Admin\RedisManager\RedisController@execute')->name('lake-redis-execute');
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Redis manager', 'lake-redis', 'fa-database');

        parent::createPermission('Redis Manager', 'ext.lake-redis-manager', 'lake-redis*');
    }
}
