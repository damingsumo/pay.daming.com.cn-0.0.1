<?php
//本文件配置本项目环境变量
/********************常量start****************************/
/****自行配置start*******/
//本服务器内网ip
define('INTERNAL_IP', '127.0.0.1');
//本服务器内网端口
define('INTERNAL_PORT', '80');
//本服务外网ip
define('EXTERNAL_IP', '210.14.145.213');
//本服务外网端口
define('EXTERNAL_PORT', '80');
/****自行配置end*******/
//上传服务器地址
define('FILE_SERVER', 'pic.bestdo.com');

/********************常量end****************************/


/********************变量start*************************/
//数据库配置
$dbServerArray = array(
    'yd_res' => 'mysql:host=rm-bp1sytr570613fyy0.mysql.rds.aliyuncs.com;port=3306;dbname=test_yd_res|test_iyd|Changdong123',
    'yd_base' => 'mysql:host=rm-bp1sytr570613fyy0.mysql.rds.aliyuncs.com;port=3306;dbname=test_yd_base|test_iyd|Changdong123',
    'yd_payment' => 'mysql:host=rm-bp1sytr570613fyy0.mysql.rds.aliyuncs.com;port=3306;dbname=test_payment|test_iyd|Changdong123',
    'yd_match' => 'mysql:host=rm-bp1sytr570613fyy0.mysql.rds.aliyuncs.com;port=3306;dbname=test_match|test_iyd|Changdong123',
);

//redis配置
$redisConfig = array('host' => '192.168.0.128', 'port' => '6379', 'db' => 0, 'password' => '');
/********************变量end*************************/