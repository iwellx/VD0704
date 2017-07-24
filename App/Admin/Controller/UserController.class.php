<?php 
/*
 * 用户控制器
 */
 
namespace Admin\Controller;

class UserController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('User');
    }
	
    /* 列表(默认首页)
     **/
    public function index(){
        $map = array ();
        $map = array('status'=>array('gt',-1),'del'=>0);
        $count      = $this->Model->where($map)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $_list = $this->Model->where ( $map )->order ('update_time desc,create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select ();
        $show       = $Page->show();
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('page_count',$Page->totalRows);//数据总条数
        $this->assign('page_num',$Page->totalPages);//总页数
        $this->assign('list',$_list);
        $this->display ();
    }
	
    /* 搜索
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：用户名 字段：username 类型：string*/
		if($post_data['s_username']!=''){
			$map['username']=array('like', '%'.$post_data['s_username'].'%');
		}
		/* 名称：昵称/姓名 字段：nickname 类型：string*/
		if($post_data['s_nickname']!=''){
			$map['nickname']=array('like', '%'.$post_data['s_nickname'].'%');
		}
		/* 名称：邮箱 字段：email 类型：string*/
		if($post_data['s_email']!=''){
			$map['email']=array('like', '%'.$post_data['s_email'].'%');
		}
		/* 名称：余额 字段：amount 类型：num*/
		if($post_data['s_amount']!=''){
			$map['amount']=$post_data['s_amount'];
		}
		/* 名称：积分 字段：point 类型：num*/
		if($post_data['s_point']!=''){
			$map['point']=$post_data['s_point'];
		}
		/* 名称：创建时间 字段：create_time 类型：datetime*/
		if($post_data['s_create_time_min']!=''){
			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
		}
		if($post_data['s_create_time_max']!=''){
			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
		}
		/* 名称：更新时间 字段：update_time 类型：datetime*/
		if($post_data['s_update_time_min']!=''){
			$map['update_time'][]=array('gt',strtotime($post_data['s_update_time_min']));
		}
		if($post_data['s_update_time_max']!=''){
			$map['update_time'][]=array('lt',strtotime($post_data['s_update_time_max']));
		}
		/* 名称：状态 字段：status 类型：select*/
		if($post_data['s_status']!=''){
			$map['status']=$post_data['s_status'];
		}
		return $map;
	}
    
    /* 添加
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_User', 'User', $result);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
	}
	
    /* 编辑
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
			$post_data["group_ids"]=I("post.group_ids");
			$post_data["group_ids"]=implode(",",$post_data["group_ids"]); 
			$data=$this->Model->create($post_data);
			//防止密码被设置为空
            if($data['password']==md5('')){
                unset($data['password']);
            }
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
				if($result){
					action_log('Edit_User', 'User', $post_data['id']);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->find();
			$this->assign('info', $_info);
        	$this->display();
		}
	}
	
	/* 查看
     **/
	public function view(){
		$_info=I('get.');
		$_info = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->find();
		$_info['group'] = '';
		if($_info['group_ids'])
		{
			$group_list = M('AuthGroup')->where(array('id'=>array('in',$_info['group_ids'],'del'=>0)))->field('title')->select();
			if($group_list)
			{
				$group = array_column($group_list,'title');
				$_info['group'] = join(',',$group);
			}
		}
		$this->assign('info', $_info);
		$this->display();
	}
	
    /* 用户组
     **/
	public function group(){
		if(IS_POST){
			$post_data=I('post.');
			$group_ids=I('post.group_ids');
			$_data['group_ids']=implode(',',$group_ids);
			$this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($_data);
			$this->success ( "操作成功！",U('index'));
		}else{
			$_info=I('get.');
			$_group_ids = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->getField('group_ids');
			$this->assign('_info', $_info);
			$this->assign('_group_id', $_group_ids);
        	$this->display();
		}
	}
	
    /* 删除
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		if($this->Model->where(array('id'=>$id,'del'=>0))->count()==0)
		{
			$this->error('用户已删除！');
		}
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_User', 'User', $id);
			$this->success('删除成功！');
		}
	}
	
    /**
     * 修改昵称
     */
    public function updateNickname(){
		if(IS_POST){
			$post_data=I('post.');
			empty($post_data['nickname']) && $this->error('请输入昵称');
			empty($post_data['password']) && $this->error('请输入密码');
			$_info = $this->Model->where(array('id'=>$this->UserInfo['id']))->find();
			if($_info['password'] != md5($post_data['password'])){
				$this->error('密码错误');
			}
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$this->UserInfo['id']))->save($data);
				if($result){
					action_log('Edit_User', 'User', $this->UserInfo['id']);
					$this->success ( "操作成功！",U('Public/logout'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
    }
	
    /**
     * 修改密码
     */
    public function updatePassword(){
		if(IS_POST){
			$post_data=I('post.');
			empty($post_data['old']) && $this->error('请输入原密码');
			empty($post_data['password']) && $this->error('请输入新密码');
			empty($post_data['repassword']) && $this->error('请输入确认密码');
			if($post_data['repassword'] != $post_data['password']){
				$this->error('两次输入的密码不一致');
			}
			$_info = $this->Model->where(array('id'=>$this->UserInfo['id']))->find();
			if($_info['password'] != md5($post_data['old'])){
				$this->error('原密码错误');
			}
			$post_data['password']=md5($post_data['password']);
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_User', 'User', $post_data['id']);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
    }
}