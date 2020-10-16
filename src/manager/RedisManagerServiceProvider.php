<?php

namespace Lake\Admin\RedisManager;

use Illuminate\Support\ServiceProvider;

use Lake\Admin\RedisManager\Command\Install;
use Lake\Admin\RedisManager\Command\Uninstall;

class RedisManagerServiceProvider extends ServiceProvider
{
    protected $commands = [
        Install::class,
        Uninstall::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lake-redis-manager');

        RedisManager::boot();
    }
}
