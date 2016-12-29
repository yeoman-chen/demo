<?php
/**
 * 多进程基础示例
 */
//echo PHP_EOL . time() . "\n";
$worker_num =3;//创建的进程数
for($i=0;$i<$worker_num;$i++)
{
	$process = new \swoole\process('callback_function_we_write');
	$pid = $process->start();
	//echo PHP_EOL . $pid;
}

function callback_function_we_write(\swoole\process $worker)
{
	/*echo PHP_EOL;
	var_dump($worker);
	echo PHP_EOL;*/

	for($i=0;$i<100000000;$i++){

	}
	//echo PHP_EOL . time() . "\n";
}

echo PHP_EOL . time() . "\n" ;
for($i=0;$i<100000000;$i++){}
for($i=0;$i<100000000;$i++){}
for($i=0;$i<100000000;$i++){}
echo PHP_EOL . time() . "\n" ;