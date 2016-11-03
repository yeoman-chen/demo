<?php

$worker_num = 5;  // 默认进程数
$workers = [];  // 进程保存
$redirect_stdout = false;  // 重定向输出  ; 这个参数用途等会我们看效果
for ($i=0; $i < $worker_num; $i++) { 
	$process = new swoole_process('callback_function',$redirect_stdout);

	// 启用消息队列 int $msgkey = 0, int $mode = 2
	//$process->useQueue(0,2);
	$pid = $process->start();

	 // 管道写入内容
	//$process->write(json_encode(['name' => '进程','pid' => $pid]));

	//$process->push('我是进程的消息队列内容');

	// 进程重命名
	$process->name('child_name_process_'.$pid.$worker_num);

	// 将每一个进程的句柄存起来
	$workers[$pid] = $process;
	
}
registerSignal($workers);
/**
 * 子进程回调
 * @param  swoole_process $worker [description]
 * @return [type]                 [description]
 */
function callback_function(swoole_process $worker)
{
    // $recv = $worker->pop();
    // echo "子输出主内容: {$recv}".PHP_EOL;
    // $worker->push("我是子进程内容");
    
    //$htmlResult = getHtml();
	$htmlResult = 'callback_function';
    //echo PHP_EOL.$htmlResult.'===='.$worker->pid."\n";

    $max = 5;
    $i = 1;
    while(true){
    	echo "current times is {$i} date('Y-m-d H:i:s')\n";
    	$dada = ['id' => 'hdada','value' => 'hello world-'.date('Y-m-d H:i:s')];
    	if($dada){
    		print_r($dada);
    		$i++;
	    	
    	}else{
    		echo 'wait for data !';
    	}
    	sleep(2);
    	if($i >= $max){
    		echo "达到最大循环次数，让子进程退出，主进程会再次拉起子进程\n";
	    	break;
	    }
    }
    //$worker->exit(0);

    // $worker->exit(0);
}

/**
 * 监控/回收子进程
 */
/*while (1) {
	$ret = swoole_process::wait();
	if($ret) { //$ret 是个数组，code是进程退出状态码
		$pid = $ret['pid'];
		echo PHP_EOL."Worker Exit, PID=" . $pid . PHP_EOL;
	}else{
		break;
	}
}*/

function registerSignal($workers){

	//监控子进程信号
	swoole_process::signal(SIGTERM,function($signo){
		echo "关闭进程";
	});

	//子进程结束信号（异步信号回调）
	 swoole_process::signal(SIGCHLD,function($signo) use(&$workers){
	 	//必须为false,非阻塞模式

	 	while(true){
	 		$ret = swoole_process::wait(false);
	 		if($ret){
	 			$pid           = $ret['pid'];
                $child_process = $workers[$pid];
                    //unset($workers[$pid]);
                echo "Worker Exit, kill_signal={$ret['signal']} PID=" . $pid . PHP_EOL;
                $new_pid           = $child_process->start();
                $workers[$new_pid] = $child_process;
                unset($workers[$pid]);
	 			echo "PID={$ret['pid']}\n";
	 		} else {
	 			break ;
	 		}
	 		
	 	}
	 });
}
