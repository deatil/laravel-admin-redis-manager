<?php

namespace Lake\Admin\RedisManager\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * install
 *
 * php artisan lake-redis-manager:install
 */
class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lake-redis-manager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'lake-redis-manager extension install';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $installSqlFile = __DIR__.'/../../resources/sql/install.sql';
        $dbPrefix = DB::getConfig('prefix');
        $sqls = file_get_contents($installSqlFile);
        $sqls = str_replace('pre__', $dbPrefix, $sqls);
        DB::unprepared($sqls);
        
        $this->info('lake-redis-manager extension install success.');
    }
}
