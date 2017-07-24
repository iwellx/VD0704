<?php

namespace User\Controller;

class UserinfoController extends UserCoreController {
	
    public function index(){
		//获取用户标识符
		$AUTH_KEY=$this->UserInfo['id'];
		//设置用户模型
		$this->Model=D('User');
		//判断请求类型
		if(IS_POST){
			//获取POST数据
			$post_data['nickname']=I('post.nickname');
			$post_data['email']=I('post.email');
			$post_data['remark']=I('post.remark');
			//自动验证完成模型
			$data=$this->Model->create($post_data);
			//判断验证结果
			if($data){
				//对数据进行修改
				$result = $this->Model->where(array('id'=>$AUTH_KEY))->save($data);
				//对修改结果进行判断
				if($result){
					//更新session中的数据
					$UserInfo = M ( 'User' )->where(array('id'=>$AUTH_KEY))->find ();
					session('UserInfo',$UserInfo);
					//提示成功
					$this->success ( "操作成功！",U('index'));
				}else{
					//获取模型错误信息
					$error = $this->Model->getError();
					//弹出错误信息
					$this->error($error ? $error : "操作失败！");
				}
			}else{
				//获取模型错误信息
                $error = $this->Model->getError();
				//弹出错误信息
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			//读取当前用户数据
			$_info = $this->Model->where(array('id'=>$AUTH_KEY))->find();
			//设置变量
			$this->assign('_info', $_info);
			//渲染页面
        	$this->display();
		}
    }
	
    /**
     * 修改密码
     */
    public function password(){
		//获取用户标识符
		$AUTH_KEY=$this->UserInfo['id'];
		//设置用户模型
		$this->Model=D('User');
		//判断请求类型
		if(IS_POST){
			//获取post提交数据
			$post_data=I('post.');
			//验证旧密码是否存在
			empty($post_data['old_password']) && $this->error('请输入原密码');
			//验证新密码是否存在
			empty($post_data['new_password']) && $this->error('请输入新密码');
			//验证重复密码是否存在
			empty($post_data['new_password2']) && $this->error('请输入确认新密码');
			//验证两次密码是否一致
			if($post_data['new_password'] != $post_data['new_password2']){
				$this->error('两次输入的新密码不一致');
			}
			//获取数据库中的当前用户信息
			$_info = $this->Model->where(array('id'=>$AUTH_KEY))->find();
			//验证旧密码是否正确
			if($_info['password'] != md5($post_data['old_password'])){
				$this->error('原密码错误');
			}
			//将新密码进行md5
			$post_data['password']=md5($post_data['new_password']);
			//验证数据
			$data=$this->Model->create($post_data);
			//判断验证数据数据的情况
			if($data){
				//对数据库中的数据进行更新
				$result = $this->Model->where(array('id'=>$AUTH_KEY))->save($data);
				//判断更新返回情况
				if($result){
					R('User/Public/logout',array('修改密码成功，请重新登录'));
				}else{
					//获取模型错误信息
					$error = $this->Model->getError();
					//弹出错误信息
					$this->error($error ? $error : "操作失败！");
				}
			}else{
				//获取模型错误信息
                $error = $this->Model->getError();
				//弹出错误信息
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
    }
	
    public function avatar(){
		$this->display ();
    }
	
}