<?php
/**
 * redisTest
 * @author chenym
 * @since 2016-06-16
 */

include 'redisServer.php';
$host = '192.168.9.130';
$port = 16379;
$redis   = new redisServer($host,$port);

//$redis->flushdb();die;
$strkey  = 'str';
$listKey = 'list';
$setKey = 'ycfSet';
$hashKey = 'hashkey';

$redis->log("=================strset==================\r\n");
for ($i = 0; $i < 10000; $i++) {
    $res = $redis->set($strkey . $i, $setKey.'string'.$i);
    $res && $redis->log('strset:' . $strkey . $i . '-value:' . $i . "\r\n");
}die;
$redis->log("=================strget==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->get($strkey . $i);
    $res && $redis->log('strget:' . $strkey . $i . '-value:' . $res . "\r\n");
}
$redis->log("\r\n\r\n");
$redis->log("=================listpush==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->lPush($listKey . $i, $i);
    $res && $redis->log('listpush:' . $listKey . $i . '-value:' . $i . "\r\n");
}

$redis->log("=================listpop==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->rPop($listKey . $i);
    $res && $redis->log('listpop:' . $listKey . $i . '-value:' . $res . "\r\n");
}

$redis->log("\r\n\r\n");
$redis->log("=================ycfset set==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->sadd($setKey, $i);
    $res && $redis->log('ycfsadd:' . $setKey . '-value:' . $i . "\r\n");
}

$redis->log("=================ycfset get==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->spop($setKey);
    $res && $redis->log('ycfspop:' . $setKey . $i . '-value:' . $res . "\r\n");
}


$redis->log("\r\n\r\n");
$redis->log("=================hashset==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->hSet($hashKey, $listKey . $i, $i);
    $res && $redis->log('hashset:' . $listKey . $i . '-value:' . $i . "\r\n");
}

$redis->log("=================hashget==================\r\n");
for ($i = 0; $i < 1000; $i++) {
    $res = $redis->hGet($hashKey, $listKey . $i);
    $res && $redis->log('hashget:' . $listKey . $i . '-value:' . $res . "\r\n");
}

exit('run over!');