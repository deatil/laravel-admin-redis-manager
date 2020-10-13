<?php

namespace Lake\Admin\RedisManager;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Redis\Connections\Connection;

use Predis\Client;
use Predis\Pipeline\Pipeline;
use Predis\Collection\Iterator\Keyspace;

use Encore\Admin\Extension;

use Lake\Admin\RedisManager\DataType\DataType;
use Lake\Admin\RedisManager\DataType\Hashes;
use Lake\Admin\RedisManager\DataType\Lists;
use Lake\Admin\RedisManager\DataType\Sets;
use Lake\Admin\RedisManager\DataType\SortedSets;
use Lake\Admin\RedisManager\DataType\Strings;

/**
 * Class RedisManager.
 */
class RedisManager extends Extension
{
    use BootExtension;

    /**
     * @var array
     */
    public static $typeColor = [
        'string' => 'primary',
        'list'   => 'info',
        'zset'   => 'danger',
        'hash'   => 'warning',
        'set'    => 'success',
    ];

    /**
     * @var array
     */
    protected $dataTyps = [
        'string' => Strings::class,
        'hash'   => Hashes::class,
        'set'    => Sets::class,
        'zset'   => SortedSets::class,
        'list'   => Lists::class,
    ];

    /**
     * @var RedisManager
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $connection;

    /**
     * Get instance of redis manager.
     *
     * @param string $connection
     *
     * @return RedisManager
     */
    public static function instance($conn = 'default')
    {
        if (!static::$instance instanceof self) {
            static::$instance = new static($conn);
        }

        return static::$instance;
    }

    /**
     * RedisManager constructor.
     *
     * @param string $connection
     */
    public function __construct($conn = 'default')
    {
        $this->connection = $conn;
    }

    /**
     * @return Lists
     */
    public function lists()
    {
        return new Lists($this->getConnection());
    }

    /**
     * @return Strings
     */
    public function string()
    {
        return new Strings($this->getConnection());
    }

    /**
     * @return Hashes
     */
    public function hash()
    {
        return new Hashes($this->getConnection());
    }

    /**
     * @return Sets
     */
    public function set()
    {
        return new Sets($this->getConnection());
    }

    /**
     * @return SortedSets
     */
    public function zset()
    {
        return new SortedSets($this->getConnection());
    }

    /**
     * Get connection collections.
     *
     * @return Collection
     */
    public function getConnections()
    {
        return collect(config('database.redis'))->filter(function ($conn) {
            return is_array($conn);
        });
    }

    /**
     * Get a registered connection instance.
     *
     * @param string $connection
     *
     * @return Connection
     */
    public function getConnection($connection = null)
    {
        if ($connection) {
            $this->connection = $connection;
        }

        return $this->getClient();
    }

    /**
     * Get a registered Client instance.
     *
     * @return Client
     */
    public function getClient()
    {
        $param = $this->getConnections()[$this->connection];
        return new Client($param);
    }

    /**
     * Get information of redis instance.
     *
     * @return array
     */
    public function getInformation()
    {
        return $this->getClient()->info();
    }

    /**
     * Scan keys in redis by giving pattern.
     *
     * @param string $pattern
     * @param int    $count
     *
     * @return array|\Predis\Pipeline\Pipeline
     */
    public function scan($pattern = '*', $count = 100)
    {
        $client = $this->getClient();
        $keys = [];

        foreach (new Keyspace($client, $pattern) as $item) {
            $keys[] = $item;

            if (count($keys) == $count) {
                break;
            }
        }

        $script = <<<'LUA'
        local type = redis.call('type', KEYS[1])
        local ttl = redis.call('ttl', KEYS[1])

        return {KEYS[1], type, ttl}
LUA;

        return $client->pipeline(function (Pipeline $pipe) use ($keys, $script) {
            foreach ($keys as $key) {
                $pipe->eval($script, 1, $key);
            }
        });
    }

    /**
     * Fetch value of a giving key.
     *
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key)
    {
        $client = $this->getClient();
        if (!$client->exists($key)) {
            return [];
        }

        $type = $client->type($key)->__toString();
        if ($type == 'list') {
            $type = 'lists';
        }

        /** @var DataType $class */
        $class = $this->{$type}();

        $options = $this->getConnections()['options'];
        $prefix = $options['prefix'];
        if($prefix){
            $key = ltrim($key, $prefix);
        }
        $value = $class->fetch($key);
        $ttl = $class->ttl($key);

        return compact('key', 'value', 'ttl', 'type');
    }

    /**
     * Update a specified key.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function update(Request $request)
    {
        $key = $request->get('key');
        $type = $request->get('type');

        /** @var DataType $class */
        $class = $this->{$type}();
        $class->update($request->all());

        $class->setTtl($key, $request->get('ttl'));
    }

    /**
     * Remove the specified key.
     *
     * @param string $key
     *
     * @return int
     */
    public function del($key)
    {
        if (is_string($key)) {
            $key = [$key];
        }

        return $this->getClient()->del($key);
    }

    /**
     * 运行redis命令.
     *
     * @param string $command
     *
     * @return mixed
     */
    public function execute($command)
    {
        $command = explode(' ', $command);

        return $this->getClient()->executeRaw($command);
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public static function typeColor($type)
    {
        return Arr::get(static::$typeColor, $type, 'default');
    }
}
