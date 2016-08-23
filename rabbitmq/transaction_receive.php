<?php
//连接RabbitMQ
$conn_args = array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest');
$conn = new AMQPConnection($conn_args);
$conn->connect();
//设置queue名称，使用exchange，绑定routingkey
$channel = new AMQPChannel($conn);
$q = new AMQPQueue($channel);
$q->setName('ttlsa_queue');
$q->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$q->declare();
//$q->bind('ttlsa_exchange', 'ttlsa_routingkey'); 
//消息获取
$messages = $q->get(AMQP_AUTOACK) ;
if ($messages){
var_dump(json_decode($messages->getBody(), true ));
}
$conn->disconnect();
?>