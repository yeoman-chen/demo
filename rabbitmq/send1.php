<?php
/**
 * fanout 广播交换器send生产者
 */
$startTime    = time();
$exchangeName = 'demo3';
$queueName    = 'hello3';
$routeKey     = 'hello3';
$message      = 'Hello World!';
//建立连接
$conection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => 'ycftest', 'login' => 'test', 'password' => '123456'));

//$conection->connect() or die('Cannot connect to the broker!\n');

if($conection->connect()){
    echo "Connected to the broker \o/";
}else{
    die("Cannot connect to the broker!\n");
}

echo 'host:' . $conection->getHost() .PHP_EOL;
try {
    $channel  = new AMQPChannel($conection);
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $exchange->setFlags(AMQP_DURABLE);
    //$exchange->declare();
    $queue = new AMQPQueue($channel);

    $queue->setName($queueName);
    $queue->setFlags(AMQP_DURABLE);
    $queue->declare();

    for ($i = 0; $i < 100000; $i++) {
        $sendMsg = $message . '-' . $i;
        echo $i . PHP_EOL;
        //$channel->startTransaction();//开启事务
        $exchange->publish($sendMsg, $routeKey);
       // $channel->commitTransaction();//提交事务
    }

} catch (AMQPChannelException $e) {
    var_dump($e);
    exit();
}
$conection->disconnect();

$endTime      = time();
echo $runTime = $endTime - $startTime;
