<?php 
/*
 * 用户组控制器
 */
 
namespace Admin\Controller;

class AuthGroupController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('AuthGroup');
    }
	
    /* 列表(默认首页)
     **/
	public function index(){
		$map = array('del'=>0);
		if(I('get.pid'))
		{
			$map = array('pid'=>I('get.pid'));
		}else{
			$map['pid'] = 0;
			$this->assign('pid',$pid);
		}
		//根据搜索条件对模型数据进行搜索
		$data = $this->Model->where($map)->order('id asc')->getField('id,pid,title,remark,status');
		foreach($data as $key=>$value)
		{
			$child = $this->Model->where(array('pid'=>$value['id'],'del'=>0))->order('id asc')->count();
			if($child>0)
			{
				$data[$key]['children'] = $child;
			}
		}
		$this->assign('list',$data);
		$this->display();
	}
	
    /* 搜索
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：用户组标题 字段：title 类型：string*/
		if($post_data['s_title']!=''){
			$map['title']=array('like', '%'.$post_data['s_title'].'%');
		}
		/* 名称：用户组状态 字段：status 类型：select*/
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
			if(I("post.rules"))
			{
				$post_data["rules"]=I("post.rules");
				$post_data["rules"]=implode(",",$post_data["rules"]); 
			}else{
				$post_data["rules"] = '';
			}
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_AuthGroup', 'AuthGroup', $result);
					$this->success ( "操作成功！",U('AuthGroup/index',array('pid'=>$post_data['jump_pid'])));
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
			$post_data["rules"]=I("post.rules");
			$post_data["rules"]=implode(",",$post_data["rules"]);
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
				if($result === false){
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}else{
					action_log('Edit_AuthGroup', 'AuthGroup', $post_data['id']);
					$this->success ( "操作成功！",U('AuthGroup/index',array('pid'=>$post_data['jump_pid'])));
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id']))->find();
			$this->assign('info', $_info);
        	$this->display();
		}
	}
	
    /* 删除
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$child = $this->Model->where(array('pid'=>$id,'del'=>0))->count();
		if($child>0)
		{
			$this->error('存在子用户组，请先删除子用户组');
		}
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_AuthGroup', 'AuthGroup', $id);
			$this->success('删除成功！');
		}
	}
}