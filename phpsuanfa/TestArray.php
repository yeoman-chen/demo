<?php
//对象可以像数组一样进行foreach循环，要求属性必须是私有的（Interator模式的PHP5实现，写一类实现Interator接口）
class TestArray implements Iterator {

	private $item = [1,2,3,4,5,6];

	public function __construct()
	{

	}
	//重置，将数组内部指针指向第一个单元 
	public function rewind(){
		reset($this->item);
	}
	//返回数组当前单元
	public function current()
	{
		return current($this->item);
	}
	//判断当前索引游标指向的元素是否有效
	public function valid(){
		return ($this->current() !== false);
	}
	//返回当前单元的键名
	public function key(){
		return key($this->item);
	}
	//移动当前索引游标到下一元素
	public function next(){
		return next($this->item);
	}
}
//测试
$t = new TestArray();var_dump($t);
echo "<br/>";
foreach ($t as $key => $value) {
	echo $key,'---->',$value,'<br />';
}
