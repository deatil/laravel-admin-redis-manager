<?php

namespace Lake\Admin\RedisManager\DataType;

use Illuminate\Support\Arr;

class SortedSets extends DataType
{
    /**
     * 查询方法 zrange（从小到大） | zrevrange (从大到小)
     */
    public function fetch($key)
    {
        return $this->getClient()->zrevrange($key, 0, -1, ['WITHSCORES' => true]);
    }

    public function update(array $params)
    {
        $key = Arr::get($params, 'key');

        if (Arr::has($params, 'member')) {
            $member = Arr::get($params, 'member');
            $score = Arr::get($params, 'score');
            $this->getClient()->zadd($key, [$member => $score]);
        }

        if (Arr::has($params, '_editable')) {
            $score = Arr::get($params, 'value');
            $member = Arr::get($params, 'pk');

            $this->getClient()->zadd($key, [$member => $score]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = Arr::get($params, 'key');
        $ttl = Arr::get($params, 'ttl');
        $score = Arr::get($params, 'score');
        $member = Arr::get($params, 'member');

        $this->getClient()->zadd($key, [$member => $score]);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }
    }

    /**
     * Remove a member from a sorted set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = Arr::get($params, 'key');
        $member = Arr::get($params, 'member');

        return $this->getClient()->zrem($key, $member);
    }
}
