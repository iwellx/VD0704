<?php

return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'    => __ROOT__ . '/Public/Admin/img',
        '__CSS__'    => __ROOT__ . '/Public/Admin/css',
        '__JS__'     => __ROOT__ . '/Public/Admin/js',
    ),
	'SHOW_PAGE_TRACE' =>false, 
	/* SESSION设置 */
	'SESSION_AUTO_START' => true, // 是否自动开启Session
	'SESSION_OPTIONS' => array (), // session 配置数组 支持type name id path expire domain 等参数
	'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
	'SESSION_PREFIX' => 'admin_', // session 前缀
    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR'     => 'Public/error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public/success', // 默认成功跳转对应的模板文件
	
	'aliyun_mq_topic' => 'Order-VD', // 主题
	'aliyun_mq_sub_topic' => 'VDPay', // 二级主题
	'aliyun_mq_url' => 'http://publictest-rest.ons.aliyun.com', // 公测环境的URL
	'aliyun_mq_acessKey' => 'RF0UhbQSn1Mv27vr', // 阿里云身份验证码
	'aliyun_mq_secretKey' => 'igqt7gA4YuLaI6bp0WtfnrzuMVhM80', // 阿里云身份验证密钥
	'aliyun_mq_producerId' => 'PID-Order-VD',//发布者ID
	'aliyun_mq_consumerId' => 'CID-Order-VD',//消费者ID

    'aliyun_mq_topic' => 'testjjicar2', // 主题
	'aliyun_mq_url' => 'http://publictest-rest.ons.aliyun.com', // 公测环境的URL
	'aliyun_mq_acessKey' => 'nsolaYCnbIS6BmaV', // 阿里云身份验证码
	'aliyun_mq_secretKey' => 'EWe7DiGgYt0czlxLDmgrRJpaqECn1u', // 阿里云身份验证密钥
	'aliyun_mq_producerId' => 'PID_testme',//发布者ID
	'aliyun_mq_consumerId' => 'CID_jjicar_test',//消费者ID
	
	'DB_BBS'=>array(
		'DB_TYPE' => 'mysql',
		'DB_HOST' => '4ac563e4055d4f78.redis.rds.aliyuncs.com',
		'DB_NAME' => '4ac563e4055d4f78',
		'DB_USER' => '4ac563e4055d4f78',
		'DB_PWD' => 'Fxft123456',
		'DB_PORT' => '3306',
	),

);