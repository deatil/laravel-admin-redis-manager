<?php

namespace Lake\Admin\RedisManager;

use Illuminate\Support\ServiceProvider;

class RedisManagerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lake-redis-manager');

        RedisManager::boot();
    }
}
