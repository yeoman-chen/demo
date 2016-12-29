<?php
//官网demo
//多人聊天，广播，server.php服务端和index.html客户端
$server = new swoole_websocket_server("0.0.0.0", 50011);

$server->on('open', function (swoole_websocket_server $server, $request) {
    global $map;//客户端集合
    echo "server: handshake success with fd{$request->fd}\n";//$request->fd 是客户端id
    $map[$request->fd] = $request->fd;
    file_put_contents( __DIR__ .'/log.txt' , $request->fd);
    //print_r($request);
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    global $map;//客户端集合
    global $client;
    $data = $frame->data;
    $m = file_get_contents( __DIR__ .'/log.txt');
    $data = $frame->data;
    for ($i=1 ; $i<= $m ; $i++) {
        echo PHP_EOL . '  i is  ' . $i .  '  data  is '.$data  . '  m = ' . $m;
        $server->push($i, $data );//循环广播
    }
    //$server->push($frame->fd, "this is server");//$frame->fd 是客户端id，$frame->data是客户端发送的数据
    //服务端向客户端发送数据是用 $server->push( '客户端id' ,  '内容')
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
