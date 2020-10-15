<?php

namespace Lake\Admin\RedisManager\DataType;

class Sets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->getClient()->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = $params['key'];

        if (!empty( $params['member'])) {
            $member = $params['member'];
            $this->getClient()->sadd($key, $member);
        }

        if (!empty( $params['_editable'])) {
            $new = $params['value'];
            $old = $params['pk'];

            $this->getClient()->transaction(function ($tx) use ($key, $old, $new) {
                $tx->srem($key, $old);
                $tx->sadd($key, $new);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = $params['key'];
        $ttl = $params['ttl'];
        $members = $params['members'];

        $this->getClient()->sadd($key, $members);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }
    }

    /**
     * Remove a member from a set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = $params['key'];
        $member = $params['member'];

        return $this->getClient()->srem($key, $member);
    }
    
    /**
     * 工具
     * sdiff 差集，sinter 交集，sunion 并集
     */
    public function tool(array $params)
    {
        $key1 = $params['key1'];
        $key2 = $params['key2'];
        
        $action = $params['action'];
        if (in_array($action, ['sdiff', 'sinter', 'sunion'])) {
            return $this->getClient()->{$action}($key1, $key2);
        } elseif (in_array($action, ['sdiffstore', 'sinterstore', 'sunionstore'])) {
            if (!isset($params['storekey'])) {
               return false; 
            }
            $storekey = $params['storekey'];
            return $this->getClient()->{$action}($storekey, $key1, $key2);
        }
        
        return false;
    }
}
