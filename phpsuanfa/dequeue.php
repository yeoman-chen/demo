<?php

//php实现双向队列
class dequeue{

	private $queue = [];

	public function __construct(){

	}
	//头部插入数据
	public function addFirst($item){
		array_unshift($this->queue, $item);
	}
	//头部弹出数据
	public function removeFirst(){
		array_shift($this->queue);
	}
	//尾部插入数据
	public function addLast($item){
		array_push($this->queue, $item);
	}
	//尾部弹出数据
	public function removeLast(){
		array_pop($this->queue);
	}
	//显示队列信息
	public function showQueue(){
		print_r($this->queue);
	}
}

$dequeue = new dequeue();
$dequeue->addFirst(1);
$dequeue->addFirst(2);
$dequeue->showQueue();
$dequeue->removeFirst();
$dequeue->showQueue();
$dequeue->addLast(1);
$dequeue->showQueue();
$dequeue->removeLast();
$dequeue->showQueue();