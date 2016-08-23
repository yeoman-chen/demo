<?php
/**
  * redis 订阅需要脚本运行
  *
  */
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$redis->subscribe(array('msg'),'callback');
function callback($instance,$channelName,$message) {
	echo $channelName,"===>",$message,PHP_EOL;
}