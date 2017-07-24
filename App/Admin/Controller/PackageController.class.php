<?php 
/*
 * 套餐控制器
 */
 
namespace Admin\Controller;

class PackageController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Package');
    }
	
    /* 列表(默认首页)
     **/
	public function index(){
		$map = array ();
		$map = array('status'=>array('gt',-1),'del'=>0);
		
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['package_name']))
		{
			$map['name'] = array('like','%'.$get_data['package_name'].'%');
			$_GET['search'] = 1;
			$page_search['package_name'] = $get_data['package_name'];
		}
		if(!empty($get_data['package_sn']))
		{
			$map['package_sn'] = array('like','%'.$get_data['package_sn'].'%');
			$_GET['search'] = 1;
			$page_search['package_sn'] = $get_data['package_sn'];
		}
		if(!empty($get_data['carrieroperator']))
		{
			$map['carrieroperator'] = array('eq',$get_data['carrieroperator']);
			$_GET['search'] = 1;
			$page_search['carrieroperator'] = $get_data['carrieroperator'];
		}
		$count      = $this->Model->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$_list = $this->Model->where ( $map )->order ('update_time desc,create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select ();
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
			$package_sn = '';
			if($post_data['carrieroperator'] == '10')
			{
				$package_sn .='LT_';
			}elseif($post_data['carrieroperator'] == '11')
			{
				$package_sn .='YD_';
			}else{
				$package_sn .='DX_';
			}
			$package_sn .= $post_data['package_value'].'M_';
			$package_sn .= $post_data['cycle_value'].strtoupper($post_data['cycle_unit']);
			if($this->Model->where(array('del'=>0,'package_sn'=>$package_sn))->count()>0)
			{
				$this->error('存在相同套餐');
			}
			$post_data['package_sn'] = $package_sn;
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Package', 'Package', $result);
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
			$company_list = M('company')->where(array('status'=>1,'del'=>0))->select();
			$this->assign('company_list',$company_list);
        	$this->display();
		}
	}
	/* 创建客户编号
     **/
	public function custom_no()
	{
		$last_id = $this->Model->order('id desc')->getField('id');
		return 'No'.str_pad($last_id+1, 11, '0', STR_PAD_LEFT);
	}
	
	/* 校验信息唯一性
     **/
	public function check_fields($value,$type,$id=0)
	{
		$result = $this->Model->check_fields($value,$type,$id);
		return $result;
	}
	
    /* 编辑
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
			$post_data['name'] = str_replace('-','',$post_data['name']);
			$post_data['card_number'] = str_replace('-','',$post_data['card_number']);
			if($post_data['card_number'])
			{
				if($this->check_fields($post_data['card_number'],'card_number',$post_data['id']) == false)
				{
					$this->error("卡号已存在！");
				}
			}
			$post_data['imsi'] = str_replace('-','',$post_data['imsi']);
			if($post_data['imsi'])
			{
				if($this->check_fields($post_data['imsi'],'imsi',$post_data['id']) == false)
				{
					$this->error("imsi已存在！");
				}
			}
			$post_data['iccid'] = str_replace('-','',$post_data['iccid']);
			if($post_data['iccid'])
			{
				if($this->check_fields($post_data['iccid'],'iccid',$post_data['id']) == false)
				{
					$this->error("iccid已存在！");
				}
			}
			$data=$this->Model->create($post_data);
			if($data){
				unset($post_data['carrieroperator']);
				unset($post_data['cycle_unit']);
				unset($post_data['cycle_value']);
				unset($post_data['package_value']);
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
				if($result){
					action_log('Edit_Package', 'Package', $post_data['id']);
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
			$company_list = M('company')->where(array('status'=>1,'del'=>0))->select();
			$this->assign('company_list',$company_list);
        	$this->display();
		}
	}
	
	/* 查看
     **/
	public function view(){
		$_info=I('get.');
		$_info = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->find();
		$_info['company_name'] = M('Company')->where(array('id'=>$_info['company']))->getField('name');
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
			$this->error('客户已删除！');
		}
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Package', 'Package', $id);
			$this->success('删除成功！');
		}
	}
}