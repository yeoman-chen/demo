<?php
/**
 * fanout 广播交换器消费者
 * $routeKey和$queueName 可以随便命名，通过与exchange绑定即可接收交换器的信息
 */
$exchangeName = 'demo3';
$queueName    = 'hello3_test';
$routeKey     = 'hello3';
$message      = 'Hello World!';

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => 'ycftest', 'login' => 'test', 'password' => '123456'));
//$connection->connect() or die("Cannot connect to the broker!\n");
if($connection->connect() ){
	echo "Connected to the broker \o/";
}else{
	die("Cannot connect to the broker!\n");
}
$channel  = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setFlags(AMQP_DURABLE);
//$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setType(AMQP_EX_TYPE_FANOUT);
//$exchange->setPrefetchCount(10);
//$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declare();
$queue->bind($exchangeName, $routeKey);

var_dump('[*] Waiting for messages. To exit press CTRL+C');
$startTime = time();
while ($message = $queue->get()) {
    $queue->consume('callback');
    $exchange->setPrefetchCount(100);
}
$connection->disconnect();

$endTime = time();

echo $totalTime = $endTime - $startTime;
function callback($envelope, $queue)
{
    $msg = $envelope->getBody();
    var_dump(" [x] Received:" . $msg.PHP_EOL.'/ndddd');
    $queue->nack($envelope->getDeliveryTag());
}
