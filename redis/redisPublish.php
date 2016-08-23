<?php
/**
  * redis 发布可以通过脚本、页面、api执行
  *
  */
$redis = new Redis(); 
$redis->connect('127.0.0.1',6379); 
$result = $redis->publish('msg', 'welcome to redis pushlish');
var_dump($result);