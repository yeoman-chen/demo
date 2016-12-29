<?php
$workers = [];
$worker_num = 2;

for($i = 0; $i < $worker_num; $i++)
{
    $process = new swoole_process('callback_function', false, false);
    $process->useQueue();
    $pid = $process->start();
    $workers[$pid] = $process;
    //echo "Master: new worker, PID=".$pid."\n";

    /*swoole_event_add($process->pipe,function($pipe) use ($process){
    	$data = $process->start();
    	echo "RECV: " .$data.PHP_EOL;
    });*/
}

function callback_function(swoole_process $worker)
{
    //echo "Worker: start. PID=".$worker->pid."\n";
    //recv data from master
    $recv = $worker->pop();

    echo "From Master: $recv\n";
    $worker->push(" \n hehe \n ");//这里子进程向主进程发送  hehe
    //sleep(2);
    //$worker->exit(0);//退出子进程,0表示正常结束，会继续执行PHP的shutdown_function，其他扩展的清理工作
    //多个子进程使用消息队列通讯一定写上 $process->exit(1)
    $worker->exit(1);
}

foreach($workers as $pid => $process)
{
    $process->push("hello worker[$pid]\n");
    $result = $process->pop();
    echo "From worker: $result\n";//这里主进程，接受到的子进程的数据
}

for($i = 0; $i < $worker_num; $i++)
{
    $ret = swoole_process::wait();//回收结束运行的子进程。父进程中得到子进程退出的事件和状态码
    $pid = $ret['pid'];
    unset($workers[$pid]);
    echo "Worker Exit, PID=".$pid.PHP_EOL;
}