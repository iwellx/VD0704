<?php 
/*
 * 企业控制器
 */
 
namespace Admin\Controller;

class CompanyController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Company');
    }
	
    /* 列表(默认首页)
     **/
	public function index(){
		$map = array('status'=>array('gt',-1),'del'=>0);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['name']))
		{
			$map['unicom_name'] = array('like','%'.$get_data['name'].'%');
			$_GET['search'] = 1;
			$page_search['name'] = $get_data['name'];
		}
		$count      = $this->Model->where ( $map )->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$_list = $this->Model->where ( $map )->order ('update_time desc,create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$_list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
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
					action_log('Add_Company', 'Company', $result);
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

			$post_data["group_ids"]=implode(",",$post_data["group_ids"]);
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
				if($result){
					action_log('Edit_Company', 'Company', $post_data['id']);
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
	
    /* 删除
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		if($this->Model->where(array('id'=>$id,'del'=>0))->count()==0)
		{
			$this->error('企业已删除！');
		}
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Company', 'Company', $id);
			$this->success('删除成功！');
		}
	}
}