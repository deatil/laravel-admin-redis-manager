<?php

namespace Lake\Admin\RedisManager\Model;

use Illuminate\Database\Eloquent\Model;

/*
 * Connection 模型
 *
 * @create 2020-10-17
 * @author deatil
 */
class Connection extends Model
{
    protected $table = 'lake_redis_connection';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $incrementing = true;
    public $timestamps = true;
}
