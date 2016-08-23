<?php

$exchangeName = 'demo';
$queueName    = 'hello';
$routeKey     = 'hello';
$message      = 'Hello World!';

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
$channel  = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declare();
$queue->bind($exchangeName, $routeKey);

var_dump('[*] Waiting for messages. To exit press CTRL+C');
while (true) {
    $queue->consume('callback');
}
$connection->disconnect();

function callback($envelope, $queue)
{
    $msg = $envelope->getBody();
    var_dump(" [x] Received:" . $msg);
    //sleep(substr_count($msg,'.'));
    $queue->nack($envelope->getDeliveryTag());
}
