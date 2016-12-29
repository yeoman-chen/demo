<?php

/**
 * 多进程基础示例-进程间通信
 * 每次创建一个进程后，就会随之创建一个管道，主进程想和哪一个进程通信，就向那个进程的管道写入/读取数据。
 */

$redirect_stdout = true; //重定向输出
$worker_num = 2;//进程数
$workers = [];//存放进程用的

for($i=0 ;$i < $worker_num;$i++) {
	$process = new swoole_process('workerFunc',$redirect_stdout);
	$pid = $process->start();
	$workers[$pid] = $process;//将每个进程的句柄存起来
}

foreach ($workers as $pid => $process) {//$process 是子进程的句柄

	$process->write("hello worker[$pid]\n");//子进程句柄向自己管道里写内容				  $process->write($data);
	echo "From Worker: " . $process->read();//子进程句柄从自己的管道里面读取信息	$process->read();
	echo PHP_EOL;
}

function workerFunc(swoole_process $worker){//这里是子进程哦
	$recv = $worker->read();
	echo PHP_EOL . "From Master : $recv\n";
	//send data to master
	$worker->write("hello master ,this pipe is " .$worker->pipe . " this pid is ". $worker->pid . "\n");
}