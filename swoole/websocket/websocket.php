<?php
/**
 *
 * 官网demo
 * 多人聊天，广播，server.php服务端和index.html客户端
 */
class websocket
{

    private $server;
    private $table;
    const CONNECT_TYPE    = 'connect';
    const DISCONNECT_TYPE = 'disconnect';
    const MESSAGE_TYPE    = 'message';
    const INIT_SELF_TYPE  = 'self_init';
    const INIT_OTHER_TYPE = 'other_init';
    const COUNT_TYPE      = 'count';

    private $avatars = [
        'http://e.hiphotos.baidu.com/image/h%3D200/sign=08f4485d56df8db1a32e7b643922dddb/1ad5ad6eddc451dad55f452ebefd5266d116324d.jpg',
        'http://tva3.sinaimg.cn/crop.0.0.746.746.50/a157f83bjw8f5rr5twb5aj20kq0kqmy4.jpg',
        'http://www.ld12.com/upimg358/allimg/c150627/14353W345a130-Q2B.jpg',
        'http://www.qq1234.org/uploads/allimg/150121/3_150121144650_12.jpg',
        'http://tva1.sinaimg.cn/crop.4.4.201.201.50/9cae7fd3jw8f73p4sxfnnj205q05qweq.jpg',
        'http://tva1.sinaimg.cn/crop.0.0.749.749.50/ac593e95jw8f90ixlhjdtj20ku0kt0te.jpg',
        'http://tva4.sinaimg.cn/crop.0.0.674.674.50/66f802f9jw8ehttivp5uwj20iq0iqdh3.jpg',
        'http://tva4.sinaimg.cn/crop.0.0.1242.1242.50/6687272ejw8f90yx5n1wxj20yi0yigqp.jpg',
        'http://tva2.sinaimg.cn/crop.0.0.996.996.50/6c351711jw8f75bqc32hsj20ro0roac4.jpg',
        'http://tva2.sinaimg.cn/crop.0.0.180.180.50/6aba55c9jw1e8qgp5bmzyj2050050aa8.jpg',
    ];
    private $nicknames = [
        '沉淀', '暖寄归人', '厌世症i', '难免心酸°', '過客。', '昔日餘光。', '独特', '有爱就有恨', '共度余生', '忆七年', '单人旅行', '何日许我红装', '醉落夕风',
    ];
    public function __construct()
    {
        $this->table = new \swoole_table(1024);
        $this->table->column('id', swoole_table::TYPE_INT, 4);
        $this->table->column('avatar', swoole_table::TYPE_STRING, 1024);
        $this->table->column('nickname', swoole_table::TYPE_STRING, 60);
        $this->table->create();

        $this->server = new \swoole_websocket_server("0.0.0.0", 50011);
        $this->server->set([
            'worker_num'      => 8,
            'deamonize'       => false,
            'max_request'     => 10000,
            'dispatch_mode'   => 2,
            'debug_mode'      => 1,
            'task_worker_num' => 8,
        ]
        );
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->start();
    }
    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数
     * @param $server 服务对象字符串
     * @param $frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
     */
    public function onOpen(swoole_websocket_server $server, swoole_http_request $req)
    {
        $fd       = $req->fd;
        $avatar   = $this->avatars[array_rand($this->avatars)];
        $nickname = $this->nicknames[array_rand($this->nicknames)];
        $data     = ['id' => $fd, 'avatar' => $avatar, 'nickname' => $nickname];
        $this->table->set($fd, $data);
        echo "server: handshake success with fd{$req->fd}\n"; //$request->fd 是客户端id

        $this->server->task([
            'to'     => [$req->fd],
            'except' => [],
            'data'   => $this->buildMsg($data, self::INIT_SELF_TYPE),
        ]);
        //broadcast a user is online
        $msg = $this->buildMsg(
            [
                'id'       => $fd,
                'avatar'   => $avatar,
                'nickname' => $nickname,
                'total'    => count($this->table),
            ], self::CONNECT_TYPE);
        $this->server->task([
            'to'     => [],
            'except' => [$fd],
            'data'   => $msg,
        ]);
        //file_put_contents(__DIR__ . '/log.txt', $req->fd);
    }
    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数
     * @param $server 服务对象字符串
     * @param $frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
     */
    public function onMessage(swoole_server $server, swoole_websocket_frame $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

        $data = $frame->data;

        /*foreach ($this->table as $value) {
        $server->push($value['id'], $data);
        }*/
        $userInfo = $this->table->get($frame->fd);

        $msg = $this->buildMsg(
            [
                'id'       => $frame->fd,
                'avatar'   => $userInfo['avatar'],
                'nickname' => $userInfo['nickname'],
                'message'  => $data,
            ], self::MESSAGE_TYPE);
        $this->server->task([
            'to'     => [],
            'except' => [$frame->fd],
            'data'   => $msg,
        ]);
        //$server->push($frame->fd, "this is server");//$frame->fd 是客户端id，$frame->data是客户端发送的数据

    }
    public function onClose(swoole_websocket_server $server, $fd)
    {
        $userInfo = $this->table->get($fd);

        $this->table->del($fd);
        echo "client {$fd} closed\n";
        $msg = $this->buildMsg([
            'id'       => $fd,
            'avatar'   => $userInfo['avatar'],
            'nickname' => $userInfo['nickname'],
            'nickname' => $userInfo['nickname'],
            'total'    => count($this->table)
            ], self::DISCONNECT_TYPE);
        $this->server->task([
            'to'     => [],
            'except' => [$fd],
            'data'   => $msg,
        ]);
    }

    public function onTask($server, $task_id, $from_id, $data)
    {
        //echo "This Task {$task_id} from Worker {$from_id}\n";
        $clients = $server->connections;

        if (count($data['to']) > 0) {
            $clients = $data['to'];
        }
        foreach ($clients as $fd) {
            if (!in_array($fd, $data['except'])) {
                $server->push($fd, $data['data']);
            }
        }
        return "Task {$task_id}'s result";
    }
    public function onFinish($serv, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        //echo "Result: {$data}\n";
    }
    public function buildMsg($data, $type, $status = 200)
    {
        return json_encode(['status' => $status,
            'type'                       => $type,
            'content'                    => $data,
        ]);
    }
}

new websocket();
