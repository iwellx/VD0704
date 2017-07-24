<?php

namespace User\Controller;
use Common\Controller\CoreController;

class PublicController extends CoreController {
	
    /**
     * 会员登陆界面 
     */
    public function login( $map = '' ) {
		$AUTH_KEY=session(C('AUTH_KEY'));
        if ($AUTH_KEY>0) {
			$this->redirect('Index/index');
        } else {
			if(IS_POST || $map!=''){
				$username = I ( "post.username", "", "trim" );
				$password = md5(I ( "post.password", "", "trim" ));
				if (empty ( $username ) || empty ( $password )) {
					$this->error ( "账户或者密码不能为空，请重新输入！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
				}
				$map = array (
						//'username|phone' => $username,
						'username' => $username,
						'password' => $password,
						'status' => 1 
				);
				$UserInfo = M ( 'User' )->where ( $map )->find ();
				
				if ($UserInfo) {
					$AG_Data=M('AuthGroup')->where (array('id'=>$UserInfo['group_ids']))->find ();
					$UserInfo['group_id']=$UserInfo['group_ids'];
					$UserInfo['group_title']=$AG_Data['title'];
					session(C('AUTH_KEY'),$UserInfo['id']);
					session('UserInfo',$UserInfo);
					M ( 'User' )->where (array('id'=>$UserInfo['id']))->save (array('last_login_ip'=>get_client_ip(),'last_login_time'=>time()));
					if(C('?ADMIN_REME')){
						$admin_reme=C('ADMIN_REME');
					}else{
						$admin_reme=3600;
					}
					if(I("post.rember_password")){
						cookie('rw',$map,$admin_reme);
					}
					action_log('User_Login', 'User', $UserInfo ['id']);
					$this->success ( "登录成功！", U ( C ( 'AUTH_USER_INDEX' ) ) );
				} else {
					cookie('rw',null);
					$this->error ( "用户名密码错误或者此用户已被禁用！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
				}
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
	
    /* 注册
     * Auth   : Ghj
     * Time   : 2016年01月09日 
     **/
	public function reg(){
		if(IS_POST){
			$this->Model=D('User');
			$post_data=I('post.');
			if($post_data['password']==''){
				$this->error('用户密码不能为空');
			}
			$post_data['password']=md5($post_data['password']);
			$post_data['status']=1;
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					$this->success ( "注册成功，请登录！",U('User/Public/login'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "注册失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
	}
	
    /* 退出登录 */
    public function logout($Message = '' ){
		if (!is_login()) {
			$this->error ( "尚未登录", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
		}else{
			session ( null );
			cookie('rw',null);
			if (session ( C ( 'AUTH_KEY' ) )) {
				$this->error ( "退出失败", U ( C ( 'AUTH_USER_INDEX' ) ) );
			}else{
				$this->success ($Message ? $Message : "退出成功！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
			}
		}
    }
}