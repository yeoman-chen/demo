<?php
/**
 * redisTest
 * @author chenym
 * @since 2016-06-16
 */
class redisServer
{
    public $redis;
    public $host = '192.168.9.130';
    public $port = 16379;
    //初始化
    public function __construct($host = '',$port = '')
    {
        $this->redis = new Redis();
        $this->host = $host ? $host : $this->host;
        $this->port = $port ? $port : $this->port;
        //$this->host = $host ;
        //$this->port = $port ;
        $this->redis->connect($this->host, $this->port);
    }
    //字符串set
    public function set($key, $value, $expireTime = 0)
    {

        if ($expireTime == 0) {
            return (bool) $this->redis->set($key, $value);
        }
        return (bool) $this->redis->setex($key, $expireTime);
    }
    //字符串set
    public function get($key)
    {
        return $this->redis->get($key);
    }

    //list 左边添加
    public function lPush($key, $value)
    {
        return $this->redis->lPush($key, $value);
    }
    //list 右边添加
    public function rPush($key, $value)
    {
        return $this->redis->rPush($key, $value);
    }
    public function lPop($key)
    {
        return $this->redis->lPop($key, $value);
    }
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    //集合set
    public function sadd($key, $value)
    {
        return $this->redis->sadd($key, $value);
    }
    //集合 pop
    public function spop($key)
    {
        return $this->redis->spop($key);
    }

    //hash
    public function hSet($key, $field, $value)
    {
        return $this->redis->HSET($key, $field, $value);
    }
    public function hGet($key, $field)
    {
        return $this->redis->HGET($key, $field);
    }
    public function flushdb()
    {
        return $this->redis->flushDb();
    }
    public function flushall()
    {
        return $this->redis->flushAll();
    }

    public function log($errmsg)
    {
        //$path     = yii::app()->basePath . '/runtime/';
        //  $filename = $path . $fileName . '.log';
        $filename = $this->host.'redislog.log';
        $fp2      = @fopen($filename, "a");
        fwrite($fp2, $errmsg);
        fclose($fp2);
    }
}