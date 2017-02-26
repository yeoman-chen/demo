<?php

class suanfa {
	/**
	* 创建多级目录
	* @param string $dir 要创建的目录
	* @param string $mode 创建目录的模式，window下可以忽略
	*/
	public function createDir($dir,$mode = 0777)
	{
	
		if(is_dir($dir)){
			die(" {$dir} is exsit !");
		}
		if(mkdir($dir,$mode,true)){//递归创建路径
			
			echo "create {$dir} successfull";

		}else{
			echo "create {$dir} failed";

		}
	}
	/**
	 * 输出404
	 */
	public function header404()
	{
		//header('content-type:text/html;charset=utf-8');
		header("http/1.1 404 not found");
		//header("location:www.baidu.com");
	}
	/**
	* 多个进程同时写入同一个文件成功
	* @param string $file 文件名称
	* @param mode $data 写入内容
	*/
	public function writeFile($file,$data)
	{
		$fp = fopen($file,'a+');
		if(flock($fp,LOCK_EX))
		{
			//获得写锁，写数据
			fwrite($fp,$data."\r\n");
			// 解除锁定
			flock($fp,LOCK_UN);
		}else{
			echo '文件正在被锁定,无法写入';
		}
	}
	/**
	* 获取url扩展名
	* @param string $url url
	*/
	public function getUrlExt($url)
	{
		$arr = parse_url($url);
		$path = basename($arr['path']);
		//echo $path;die;
		$res = explode('.',$path);

		return $res[count($res)-1];
	}
	/**
	* 获取文件扩展名
	* @param string $file 文件
	*/
	public function getFileExt($file)
	{
		//方法1
		$path = basename($file);
		//echo $path;
		$arr = explode('.',$path);
		echo $arr[count($arr) - 1]."<br/>";
		//方法2
		$last = strrpos($file,'.');
		echo substr($file,$last+1,strlen($file))."<br/>";

		//方法3
		echo end(explode('.',$file))."<br/>";
		//方法4
		$pathinfo = pathinfo($file);
		echo $pathinfo['extension']."<br/>";
		//方法5
		echo pathinfo($file,PATHINFO_EXTENSION);
	}
	/**
	* 遍历一个文件夹下的所有文件和子文件夹
	* @param string $dir 目录路径
	*/
	public function myscanDir($dir)
	{
		$files = [];
		
		if(is_dir($dir)){
			
			if($handle = opendir($dir)){//打开目录

				while(($file = readdir($handle)) !== false){//读取目录的内容
					if($file != '.' && $file != '..'){
						if(is_dir($dir.'/'.$file)){
							$files[$file] = $this->myscanDir($dir.'/'.$file);
						}else{
							$files[] = $dir.'/'.$file;
						}
					}
					
				}
				closedir($handle);
				return $files;
			}
		}	
	}
	//验证电子邮件的格式是否正确
	public function checkEmail($email)
	{
		$preg = '/^[\w\-\.]+@[\w\-]+(\.\w+)+$/';
		return preg_match($preg, $email);
	}
	//验证日期是否正确
	public function checkDateTime($date)
	{
		$strTime = strtotime($date);
		if(date('Y-m-d H:i:s',$strTime) === $date){
			return true;
		}else{
			return false;
		}
	}
	//取上一月的最后一天
	public function getLastMonthLastDay($date = '')
	{
		date_default_timezone_set("PRC");
		if($date != ''){
			$dateTime = strtotime($date);
		}else{
			$dateTime = time();
		}
		//获取当月的第几天
		$day = date('j',$dateTime);
		//echo $day;die;
		return date('Y-m-d H:i:s',strtotime("- {$day}day"));
	}

	/**
	 * 二维数组排序
	 * @param array $array 二维数组
	 * @param string $keys 排序key
	 * @param int $order 1升序 0降序
	 */
	public function arraySort($array,$keys,$order = 1)
	{
		$tempArr = $sortArr = [];
		if(!is_array($array)){
			return false;
		}
		foreach ($array as $key => $value) {
			$tempArr[$key] = $value[$keys];
		}
		if($order == 1){
			asort($tempArr);
		}else{
			arsort($tempArr);
		}
		foreach ($tempArr as $key => $value) {
			$sortArr[] = $array[$key];
		}
		return $sortArr;
	}
	/**
	 * 冒泡排序算法
	 * @param array $arr 二维数组
	 */
	public function handleSort($arr){
		
		if(!is_array($arr)){
			return false;
		}
		$total = count($arr);
		for($i=0;$i<$total;$i++){
			for($j = 1 ; $j < $total - $i ; $j++){
				if($arr[$j-1] < $arr[$j]){
					$temp = $arr[$j];
					$arr[$j] = $arr[$j-1];
					$arr[$j-1] = $temp;
				}
			}
		}
		return $arr;
	}
	/**
	 * 约瑟夫环问题
	 * 一群猴子排成一圈，按1，2，...，n依次编号。然后从第1只开始数，数到第m只,把它踢出圈，从它后面再开始数，再数到第m只，在把它踢出去...，如此不停的进行下去，直到最后只剩下一只猴子为止，那只猴子就叫做大王。要求编程模拟此过程，输入m、n,输出最后那个大王的编号
	 * @param int $n 共n个数据
	 * @param int $m 第m个提出
	 */
	public function getKingMonkey($n,$m)
	{
		$monkey = range(1,$n);
		$i = 0;
		while (count($monkey) > 1){
			$i += 1;
			$temp = array_shift($monkey);//踢出头部的猴子
			if( $i % $m != 0){
				array_push($monkey, $temp);//把猴子放回末尾
			}
		}
		return $monkey[0];//返回最后一个
	}
	 /**
     * 顺序查找
     * @param  array $arr 数组
     * @param  string $sval   要查找的元素
     * @return   mixed  成功返回数组下标，失败返回-1
     */
	public function seqSearch($arr,$sval)
	{
		$total = count($arr);
		for($i=0;$i<$total;$i++){
			if($arr[$i] == $sval){
				break;
			}
		}
		if($i < $total){
			return $i;
		}else{
			return -1;
		}
	}
	/**
     * 二分查找
     * @param  array $arr 数组(已排好顺序)
     * @param  string $sval   要查找的元素
     * @return   mixed  成功返回数组下标，失败返回-1
     */
	public function binSearch($arr,$low,$high,$sval){
		if($low <= $high){
			$mid = intval(($low + $high)/2);
			if($arr[$mid] == $sval){
				return $mid;
			}elseif( $arr[$mid] < $sval){
				return $this->binSearch($arr,$mid+1,$high,$sval);
			}else{
				return $this->binSearch($arr,$low,$mid-1,$sval);
			}
		}
		return -1;
	}
	/**
     * 公平洗牌，随机洗牌1
     * @return  array $arr 数组(随机数组)
     */
	public function washCard1()
	{
		$arr = range(1,54);
		$rarr = [];

		while (count($arr) > 0) {
			shuffle($arr);//把数组打乱
			$rarr[] = array_shift($arr);
		}

		return $rarr;
	}
	/**
     * 公平洗牌，随机洗牌2
     * @return  array $arr 数组(随机数组)
     */
	public function washCard2()
	{
		$arr = range(1,54);
		$total = count($arr);
		$rarr = [];
		for($i = 0; $i < $total; $i++){
			$index = rand(0,$total - 1 -$i);
			$rarr[] = $arr[$index];
			unset($arr[$index]);
			$arr = array_values($arr);
		}
		return $rarr;

	}
}
//多个进程同时写入同一个文件成功
//写一个函数，尽可能高效的，从一个标准url里取出文件的扩展名
//写一个函数，能够遍历一个文件夹下的所有文件和子文件夹
//编写一个函数，递归遍历，实现无限分类
//请写一个函数验证电子邮件的格式是否正确
//PHP中如何判断一个字符串是否是合法的日期模式：2007-03-13 13:13:13。要求代码不超过5行。
//编写函数取得上一月的最后一天

//=========================================
//使对象可以像数组一样进行foreach循环，要求属性必须是私有。(Iterator模式的PHP5实现，写一类实现Iterator接口)
//用PHP实现一个双向队列
//请使用冒泡排序法对以下一组数据进行排序10 2 36 14 10 25 23 85 99 45
//.一群猴子排成一圈，按1，2，...，n依次编号。然后从第1只开始数，数到第m只,把它踢出圈，从它后面再开始数，再数到第m只，在把它踢出去...，如此不停的进行下去，直到最后只剩下一只猴子为止，那只猴子就叫做大王。要求编程模拟此过程，输入m、n,输出最后那个大王的编号。
//描述顺序查找和二分查找（也叫做折半查找）算法，顺序查找必须考虑效率，对象可以是一个有序数组
//我们希望开发一款扑克游戏，请给出一套洗牌算法，公平的洗牌并将洗好的牌存储在一个整形数组里


$sufa = new suanfa();
$dir = "dadad/dadtt";
//$sufa->createDir($dir);
//$sufa->writeFile('daadad.txt',123);
$url = 'http://www.sina.com.cn/abc/de/fg.php?id=1';
//echo $sufa->getUrlExt($url);

//echo $sufa->getFileExt('D:\wnmp\www\swoole\test\phpms\suanfa.php');
//echo dirname(__FILE__);

//$resArr = $sufa->myscanDir("/mnt/hgfs/www/swoole/test");
//print_r($resArr);

/*$email = 'fafaf@dda.ccc.ccc';
var_dump($sufa->checkEmail($email)) ;*/

//var_dump($sufa->checkDateTime('2017-01-19 12:22:22'));

//var_dump($sufa->getLastMonthLastDay());

//测试
/*    $person=array(
        array('id'=>2,'name'=>'zhangsan','age'=>23),
        array('id'=>5,'name'=>'lisi','age'=>28),
        array('id'=>3,'name'=>'apple','age'=>17)
    );
    $result = $sufa->arraySort($person,'name',1);
    print_r($result);*/

/*$arr = array(10,2,36,14,10,25,23,85,99,45);
    print_r($sufa->handleSort($arr));*/

//echo $sufa->getKingMonkey(3,2);
 // 测试：顺序查找
    /*$arr1 = array(9,15,34,76,25,5,47,55);
    echo $sufa->seqSearch($arr1,47);//结果为6*/
 //测试二分查找法
/*$arr1 = [1,2,3,4,5,8,12,13,15,18];
echo $sufa->binSearch($arr1,0,9,15);//结果为8*/

//随机洗牌测试
$res = $sufa->washCard2();
print_r($res);