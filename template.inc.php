<?php
//设置uft-8编码
header('Content-Type:text/html;charset=utf-8');
//网站根目录
define('ROOT_PATH',dirname(__FILE__));
//模板文件目录
define('TPL_DIR', ROOT_PATH.'/templates/');
//编译目录
define('TPL_C_DIR', ROOT_PATH.'/templates_c/');
//缓存目录
define('CACHE', ROOT_PATH.'/cache/');
//是否开启缓冲区
define('IS_CACHE', false);
//判断是否开启缓冲区
IS_CACHE ? ob_start() : null;
//引入模板类
require ROOT_PATH.'/includes/Templates.class.php';
//实例化模板类
$_tpl=new Templates();
?>