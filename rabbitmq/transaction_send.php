<?php
//连接RabbitMQ
$conn_args = array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest');
$conn = new AMQPConnection($conn_args);
$conn->setTimeout(1);
$conn->connect();
//创建exchange名称和类型
$channel = new AMQPChannel($conn);
$ex = new AMQPExchange($channel);
$ex->setName('ttlsa_exchange');
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$ex->declare();
//创建queue名称，使用exchange，绑定routingkey
$q = new AMQPQueue($channel);
$q->setName('ttlsa_queue');
$q->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$q->declare();
$q->bind('ttlsa_exchange', 'ttlsa_routingkey');
//消息发布
$channel->startTransaction();
$message = json_encode(array('Hello World!','DIRECT'));
$ex->publish($message, 'ttlsa_routingkey');
$channel->commitTransaction();
$conn->disconnect();
?>