<?php 

include 'redisServer.php';

$host = '192.168.9.134';
$port = 6379;

$redis   = new redisServer($host,$port);
$strkey  = 'str';
$listKey = 'list';
$hashKey = 'hashkey';

/*$redis->log("=================strset==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->set($strkey . $i, $i);
    $res && $redis->log('strset:' . $strkey . $i . '-value:' . $i . "\r\n");
}*/
$redis->log("=================strget==================\r\n");
for ($i = 0; $i < 100000; $i++) {
    $res = $redis->get($strkey . $i);
    $res && $redis->log('strget:' . $strkey . $i . '-value:' . $res . "\r\n");
}die;
/*$redis->log("\n\n");
$redis->log("=================listpush==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->lPush($listKey . $i, $i);
    $res && $redis->log('listpush:' . $listKey . $i . '-value:' . $i . "\r\n");
}

$redis->log("=================listpop==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->rPop($listKey . $i);
    $res && $redis->log('listpop:' . $listKey . $i . '-value:' . $res . "\r\n");
}*/

$redis->log("\r\n\r\n");
/*$redis->log("=================hashset==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->hSet($hashKey, $listKey . $i, $i);
    $res && $redis->log('hashset:' . $listKey . $i . '-value:' . $i . "\r\n");
}*/

$redis->log("=================hashget==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->hGet($hashKey, $listKey . $i);
    $res && $redis->log('hashget:' . $listKey . $i . '-value:' . $res . "\r\n");
}

exit('run over!');
