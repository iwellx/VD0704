<?php

return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'    => __ROOT__ . '/Public/User/images',
        '__CSS__'    => __ROOT__ . '/Public/User/css',
        '__JS__'     => __ROOT__ . '/Public/User/js',
    ),
	
	"AUTH_USER_INDEX" => "User/Index/index",//网关
	"AUTH_USER_GATEWAY" => "User/Public/login",//网关
    /* 后台错误页面模板 */
    //'TMPL_ACTION_ERROR'     => 'Public/error', // 默认错误跳转对应的模板文件
    //'TMPL_ACTION_SUCCESS'   =>  'Public/success', // 默认成功跳转对应的模板文件
	
	'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
	'SESSION_PREFIX' => 'user_', // session 前缀
	
	'COOKIE_PREFIX' => 'user_', // Cookie前缀 避免冲突
);