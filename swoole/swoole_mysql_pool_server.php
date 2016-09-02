<?php
#Task进阶：MySQL连接池server端
class MySQLPool
{
	private $serv;
	private $pdo;

	//初始化
	public function __construct() {

		$this->serv = new swoole_server("127.0.0.1",9501);
		$this->serv->set(array(
			'worker_num' => 8,
			'daemonize' => false,
			'max_request' => 10000,
			'dispatch_mode' => 3,
			'debug_mode' => 1,
			'task_worker_num' => 8
			));

		$this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
                // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
	}

	public function onWorkerStart($serv,$worker_id) {

		echo "onWorkerStart\n";
		//判断是否为Task Worker进程
		if($worker_id >= $serv->setting['worker_num']) {
			$this->pdo = new PDO(
				"mysql:host=127.0.0.1;port=3306;dbname=test",
				"root",
				"",
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_PERSISTENT => true
				)
			);
		}
	}

	public function onConnect($serv , $fd,$from_id) {
		echo "Client {$fd} connect\n";
	}

	public function onReceive(swoole_server $serv , $fd ,$from_id,$data){

		$sql = array(
			/*'sql' => 'Insert into test (`pid`,`name`) values (:pid,:name)',//新增10000数据16秒
			'param' => array(
				':pid' => 0,
				':name' => "'name'"
				),*/
			'sql' => 'select * from test where id = ?',//10000次查询耗时6秒
			'param' => array(1),
			'fd' => $fd
		);
		$serv->task(json_encode($sql));
	}

	public function onClose($serv,$fd,$from_id){
		echo "Client {$fd} close connection\n";
	}

	public function onTask($serv , $fd , $from_id,$data){
		try{
			$sql = json_decode($data,true);

			$statement = $this->pdo->prepare($sql['sql']);
			$statement->execute($sql['param']);

			$serv->send($sql['fd'],"Insert");
			return true;
		} catch (PDOException $e) {
			var_dump($e);
			return false;
		}
	}

	public function onFinish($serv,$task_id,$data){
		echo $task_id;
	}
}

new MySQLPool();