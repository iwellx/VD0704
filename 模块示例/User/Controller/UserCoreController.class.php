<?php
namespace User\Controller;
use Common\Controller\CoreController;

class UserCoreController extends CoreController {
	
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->display ('Public/404');
	}
	
	//核心继承
    protected function _initialize() {
		//继承CoreController的初始化函数
        parent::_initialize();
		
		$AUTH_KEY=session(C('AUTH_KEY'));
		//判断认证key如果小于1，跳转到后台登录网关
		if( $AUTH_KEY < 1 ) {
			redirect(U(C('AUTH_USER_GATEWAY')));
		}
	}
}