<?php
if(!function_exists('curl_init')){echo '服务器不支持curl扩展';}
set_time_limit(120);
if(!defined('__DIR__') )
{
  define('__DIR__',dirname(__FILE__)) ;
}
session_start();

$configSites= array
(
    "joyanhui.cn"=>array('target_url'=>'https://leiyanhui.com',0),
    "joyanhui2.cn"=>array('target_url'=>'https://leiyanhui2.com',0)
);
//https和http
$user_url_protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://": "http://";
$user_url = $user_url_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$target_url=$configSites[$_SERVER['HTTP_HOST']]['target_url'].$_SERVER['REQUEST_URI'];
if($_SERVER['REQUEST_URI'] !="/favicon.ico"){
	setcookie('__user_url',$user_url,time()+10*60,'/');
	setcookie('__target_url',$target_url,time()+10*60,'/');
}

// 增加一个判断 比如 ： 除非url 里面包含自定字符 否则不更新
 get_file_from_server($target_url,$configSites[$_SERVER['HTTP_HOST']]['target_url']);

/*
$_SERVER['REQUEST_URI']  后缀  $_SERVER['HTTP_HOST'] 域名 $user_url 当前完整地址 $target_url 目标地址
*/
// 实际地址 以及 基础地址（用于除了head跳转）
function get_file_from_server($Geturl,$BaseUrl=''){
	$cookie_file = dirname(__FILE__).'/cookie.txt';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Geturl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面 必须要抓一下，不然后面获取内容有问题
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies 这个也存在问题 无法自动跳转登陆的问题
	curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
	 if (count($_POST) > 0)  
		{   //存在POST数据需要提交 
		   curl_setopt($ch, CURLOPT_POST, 1); // POST数据
		   curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);// 把post的变量加上
		}
	$content = curl_exec($ch);
	$contentType = curl_getinfo($ch,CURLINFO_CONTENT_TYPE); //获取文件类型
	$TrueUrl = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL); //获取实际地址，防止url跳转后紊乱 这个必须放到 curl_exec后面
	curl_close($ch);//关闭
	if ($TrueUrl != $Geturl){// 需要跳转 
		$user_url_protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://": "http://"; //获取http或https
		$GotoUrl= $user_url_protocol.str_replace($BaseUrl,$_SERVER['HTTP_HOST'],$TrueUrl ); //替换域名
		header("Location:".$GotoUrl);
	}
	else{
		header('Content-type: '.$contentType.''); //文件类型
		echo $content; //输出内容
	}
}
?>
