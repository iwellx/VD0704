<?php

namespace Admin\Controller;
use Admin\Model\UserModel;
use Common\Controller\CoreController;

class PublicController extends CoreController {
    public function login($map = ''){
        if(IS_POST || $map!=''){
			if($map==''){
				$username = I ( "post.username", "", "trim" );
				$password = md5(I ( "post.password", "", "trim" ));
				if (empty ( $username ) || empty ( $password )) {
					$this->error ( "用户名或者密码不能为空，请重新输入！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
				}
				$map = array (
						'username' => $username,
						'del' => 0 
				);
			}
			$UserInfo = M ( 'User' )->where ( $map )->find ();
			if ($UserInfo) {
				if($UserInfo['status'] != 1)
				{
					$this->error ( "用户已禁用！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
				}
				if($UserInfo['password'] != $password)
				{
					$this->error ( "用户密码错误！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
				}
				$AG_Data=M('AuthGroup')->where (array('id'=>$UserInfo['group_ids'],'del'=>0))->find ();
				$UserInfo['group_id']=$UserInfo['group_ids'];
				$UserInfo['group_title']=$AG_Data['title'];
                /********没必要存在的判断
				if(!in_array($UserInfo['group_id'],array(1,2)) && !(in_array ($UserInfo['id'], C ( 'AUTH_ADMIN' ) ))){
					$this->error ( '权限不足，无法登陆！' ,U('Public/login'));
				}
                 **********/
				session(C('AUTH_KEY'),$UserInfo['id']);
				session('UserInfo',$UserInfo);
				if(C('?ADMIN_REME')){
					$admin_reme=C('ADMIN_REME');
				}else{
					$admin_reme=3600;
				}
				if(I("post.rember_password")){
					cookie('rw',$map,$admin_reme);
				}
				action_log('Admin_Login', 'User', $UserInfo ['id']);
                /****更新登录IP,登录时间***/
                $ip_info = new UserModel();
                $ip = $ip_info->get_ip();
                $data['last_login_ip'] = $ip;
                $data['last_login_time'] = time();
                M('User')->where(array('username'=>$username))->save($data);
                /****更新登录IP,登录时间***/
				$this->redirect (U ( C ( 'AUTH_USER_INDEX' ) ) );
			} else {
				$this->error ( "用户不存在！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
			}
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
				$map=cookie('rw');
				if((count($map)>0)){
                	$this->login($map);
				}else{
                	$this->display();
				}
            }
        }
    }

    /* 退出登录 */
    public function logout(){
		if (!is_login()) {
			$this->redirect (U ( 'public/login' ) );
		}else{
			action_log('Admin_Logout', 'User', is_login());
			session ( null );
			cookie('rw',null);
			if (session ( C ( 'AUTH_KEY' ) )) {
				$this->error ( "退出失败", U ( C ( 'AUTH_USER_INDEX' ) ) );
			}else{
				$this->success ( "退出成功！", U ( 'public/login' ) );
			}
		}
    }

}
