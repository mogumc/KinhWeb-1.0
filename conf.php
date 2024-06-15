<?php
error_reporting(0);
if(!defined("ONINDEX")){
	die("禁止访问！");
}
require_once "function.php";
//请不要随意更改上方内容

$bduss = "";//请在""内填入您百度账户的BDUSS
$link = "";//若存在加速链接,请填写您的加速链接
$title = "KinhWeb";//您可以修改此处以修改网站标题
$foot = "测试版本 仅供参考";//您可以修改此处以修改网站页脚
$theme = "weui";//您可以通过修改此处更换主题 主题可以透过社区获取

//请不要随意更改下方内容
define("BDUSS",$bduss);
define("ACLINK",$link);
define("TITLE",$title);
define("FOOT",$foot);