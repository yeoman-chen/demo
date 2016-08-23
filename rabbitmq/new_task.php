<?php
$exchangeName = 'demo';
$queueName    = 'task_queue';
$routeKey     = 'task_queue';
$message      = empty($argv[1]) ? 'Hello World!' : ' ' . $argv[1];

$connection = new AMQPConnection(array(
    'host'     => '127.0.0.1',
    'port'     => '5672',
    'vhost'    => '/',
    'login'    => 'guest',
    'password' => 'guest',
));
$connection->connect() or die("Cannot connect to the broker!\n");

$channel  = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declare();
//$exchange->publish ( $message, $routeKey );
$array = array('.', '..', '...', '');
for ($i = 0; $i < 100000; $i++) {
    //$sendMsg = $message.'-'.$i;
    //$sendMsg = empty($argv[1]) ? 'Hello World!' : ' '.$argv[1];
    $sendMsg = 'hello world!' . $array[rand(0, 3)];
    echo $sendMsg . PHP_EOL;
    $exchange->publish($sendMsg, $routeKey);
}

var_dump("[x] Sent $message");

$connection->disconnect();
