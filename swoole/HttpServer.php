<?php
class Server
{
	
	private $http;
	
	public function __construct(){
		
		$this->http = new swoole_http_server("127.0.0.1",9501);
		
		$this->http->set(
				array(
					'worker_num' => 16,
					'deamonize' => false,
					'max_request' => 10000,
					'dispatch_mode' => 1	
				)
				);
		$this->http->on('Start',array($this,'onStart'));
		//$this->http->on('message' , array( $this , 'onMessage'));
		//$this->http->on('request',array($this,'onRequest'));
		$this->http->on('request', function ($request, $response) {
			if (isset($request->request_uri)) {
				$_SERVER['REQUEST_URI'] = $request->request_uri;
				echo $_SERVER['REQUEST_URI'].'\r\n';
			}
			if(isset($request->get)){
				foreach ($request->get as $key => $value) {
					# code...
					$_GET[$key] = $value;
				}
			}
			if(isset($request->post)){
				foreach ($request->post as $key => $value) {
					# code...
					$_POST[$key] = $value;
				}
			
			}
			$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>".json_encode($request->post));
		});
		//$this->http->on('message' , array( $this , 'onMessage'));
		$this->http->start();
	}
	
	public function onStart($serv){
		echo "Start\n welcome to swoole";
	}
	
	public function onRequest($request,$response){
			if (isset($request->request_uri)) {
                $_SERVER['REQUEST_URI'] = $request->request_uri;
                echo $_SERVER['REQUEST_URI'].'\r\n';
            }
            if(isset($request->post)){
            	$_SERVER['POST'] = $request->post;
            	print_r($request->post);
            }print_r($request);
		//echo $request->message;
		$response->end("<h1>Hello Swoole.</h1>");
	}
	
	public function onMessage($request,$response){
		echo $request->message;
		$response->message(json_encode(array("data1", "data2")));
	}
}

new Server();