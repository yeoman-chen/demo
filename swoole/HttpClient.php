<?php
class Client
{
	private $client;
	
	public function __construct(){
		$this->client = new swoole_client(SWOOLE_SOCK_TCP);
	}
	
	public function connect(){
		if(!$this->client->connect("127.0.0.1",9501,1)){
			echo "Error:{$fp->errMsg}[{$fp->errCode}]\n";
		}
		
		$msg_normal = "This is a Msg";
		$msg_eof = "This is a Msg\r\n";
		$msg_length = pack("N",strlen($msg_normal)).$msg_normal;
		
		$i = 0;
		
		while($i < 10){
			$this->client->send($msg_normal);
			$i++;
			echo $i.PHP_EOL;
		}
		
	}
}

$client = new Client();
$client->connect();