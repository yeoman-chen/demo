<?php

$exchangeName = 'demo';
$queueName    = 'hello';
$routeKey     = 'hello';
$message      = 'Hello World!';
$startTime    = microtime(true);
//建立连接
$conection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));

$conection->connect() or die('Cannot connect to the broker!\n');

try {
    $channel  = new AMQPChannel($conection);
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $queue = new AMQPQueue($channel);

    $queue->setName($queueName);
    $queue->setFlags(AMQP_DURABLE);
    $array = array('.', '..', '...', '');
    for ($i = 0; $i < 100000; $i++) {
        //$sendMsg = $message.'-'.$i;
        //$sendMsg = empty($argv[1]) ? 'Hello World!' : ' '.$argv[1];
        $sendMsg = 'hello world!' . $array[rand(0, 3)];
        echo $sendMsg . PHP_EOL;
        $exchange->publish($sendMsg, $routeKey);
    }

} catch (AMQPChannelException $e) {
    var_dump($e);
    exit();
}
$conection->disconnect();

$endTime      = microtime(true);
echo $runTime = $endTime - $startTime;
