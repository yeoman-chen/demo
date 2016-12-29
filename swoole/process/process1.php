<?php
/**
 * 多进程基础示例-调用不同函数
 */
$funcMap = ['methodOne','methodTwo','methodThree'];

$worker_num = 3;//创建的进程数

for($i=0;$i<$worker_num;$i++){
	$process = new swoole_process($funcMap[$i]);
	$pid = $process->start();
	sleep(2);
}

while (1) {
	$ret = swoole_process::wait();
	if($ret){
		// $ret 是个数组 code是进程退出状态码，
		$pid = $ret['pid'];
		echo PHP_EOL."Worker Exit, PID=" . $pid . PHP_EOL;
	}else{
		break;
	}
}

function methodOne(swoole_process $worker){//第一个处理的函数
	echo $worker->callback.PHP_EOL;
}

function methodTwo(swoole_process $worker){//第二个处理的函数
	echo $worker->callback.PHP_EOL;
}
function methodThree(swoole_process $worker){//第二个处理的函数
	echo $worker->callback.PHP_EOL;
}