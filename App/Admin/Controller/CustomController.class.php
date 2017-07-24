<?php 
/*
 * 客户控制器
 */
 
namespace Admin\Controller;

class CustomController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Custom');
    }
	
    /* 列表(默认首页)
     **/
	public function index(){
		$map = array ();
		$map = array('status'=>array('gt',-1),'del'=>0);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['card_number']))
		{
			$map['card_number'] = array('like','%'.$get_data['card_number'].'%');
			$_GET['search'] = 1;
			$page_search['card_number'] = $get_data['card_number'];
		}
		if(!empty($get_data['imsi']))
		{
			$map['imsi'] = array('like','%'.$get_data['imsi'].'%');
			$_GET['search'] = 1;
			$page_search['imsi'] = $get_data['imsi'];
		}
		if(!empty($get_data['company_name']))
		{
			$company = M('Company')->where(array('unicom_name'=>array('LIKE','%'.$get_data['company_name'].'%')))->field('id')->select();
			if($company)
			{
				$company_ids = array_map(function($val) use ($key){return $val['id'];},$company);
				$map['company'] = array('IN',$company_ids);
			}
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		$count      = $this->Model->where ( $map )->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$_list = $this->Model->where ( $map )->order ('update_time desc,create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select ();
		$up_mod = M('UserPackage');
		$p_mod = M('Package');
		$c_mod = M('Company');
		foreach($_list as $key=>$value)
		{
			$has_package = $up_mod->where(array('custom_no'=>$value['custom_no'],'del'=>0))->field('package_sn,service_start_time,service_end_time')->select();
			if($has_package)
			{
				$start_times = array_map(function($val) use ($key){return $val['service_start_time'];},$has_package);
				sort($start_times);
				$_list[$key]['service_start_time'] = $start_times[0];
				$end_times = array_map(function($val) use ($key){return $val['service_end_time'];},$has_package);
				rsort($end_times);
				$_list[$key]['service_end_time'] = $end_times[0];
				$has_package_ids = array_map(function($val) use ($key){return $val['package_sn'];},$has_package);
				$_list[$key]['has_package_list'] = $p_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'package_sn'=>array('in',$has_package_ids)))->field('id,name,carrieroperator')->select();
			}else{
				$_list[$key]['has_package_list'] = array();
			}
			$_list[$key]['company_name'] = $c_mod->where(array('id'=>$value['company']))->getField('unicom_name');
		}
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
			$post_data['name'] = str_replace('-','',$post_data['name']);
			$post_data['card_number'] = str_replace('-','',$post_data['card_number']);
			if($post_data['card_number'])
			{
				if($this->check_fields($post_data['card_number'],'card_number') == false)
				{
					$this->error("卡号已存在！");
				}
			}
			$post_data['imsi'] = str_replace('-','',$post_data['imsi']);
			if($post_data['imsi'])
			{
				if($this->check_fields($post_data['imsi'],'imsi') == false)
				{
					$this->error("imsi已存在！");
				}
			}
			$post_data['iccid'] = str_replace('-','',$post_data['iccid']);
			if($post_data['iccid'])
			{
				if($this->check_fields($post_data['iccid'],'iccid') == false)
				{
					$this->error("iccid已存在！");
				}
			}
			$post_data['custom_no'] = $this->custom_no();
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Customer', 'Coustom', $result);
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
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
				if($result){
					action_log('Edit_Custom', 'Custom', $post_data['id']);
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
		$_info['company_name'] = M('Company')->where(array('id'=>$_info['company']))->getField('unicom_name');
		$_info['cost_value'] = M('UserPackageCost')->where(array('custom_no'=>$_info['custom_no'],'type'=>10))->getField('cost_value');
		$this->assign('info', $_info);
		$has_package = M('UserPackage')->where(array('custom_no'=>$_info['custom_no'],'del'=>0))->field('package_sn,open_price,expense_price,service_start_time,service_end_time')->select();
		$package_mod = M('package');
		if($has_package)
		{
			$has_package_ids = array_unique(array_map(function($val) use ($key){return $val['package_sn'];},$has_package));
			$has_package_list = $package_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'package_sn'=>array('in',$has_package_ids)))->field('id,package_sn,name,carrieroperator')->select();
			foreach($has_package_list as $key=>$value)
			{
				foreach($has_package as $k=>$v)
				{
					if($value['package_sn'] == $v['package_sn'])
					{
						$has_package_list[$key]['service_start_time'] = $v['service_start_time'];
						$has_package_list[$key]['service_end_time'] = $v['service_end_time'];
						$has_package_list[$key]['open_price'] = intval($v['open_price']);
						$has_package_list[$key]['expense_price'] = intval($v['expense_price']);
					}
				}
			}
		}else{
			$has_package_list = array();
		}
		$this->assign('has_package_list', $has_package_list);
		$this->display();
	}
	
	/* 绑定套餐
     **/
	public function bindPackage(){
		if(IS_POST){
			$post_data=I('post.');
			if(empty($post_data['custom_no']))
			{
				$this->error('请指定要绑定的客户！');
			}
			if(empty($post_data['package']))
			{
				$this->error('请选择要绑定的套餐！');
			}
			if($post_data['cost_value'] == '')
			{
				$this->error('请输入已消费的流量套餐！');
			}else{
				if($post_data['cost_value'] != abs(intval($post_data['cost_value'])))
				{
					$this->error('输入的已消费流量格式有误！');
				}
			}
			$data=D('Package')->deal_bind($post_data);
			if($data !== false){
				action_log('Bind_CustomPackage', 'Custom', $post_data['custom_no']);
				$this->success ( "操作成功！",U('index'));
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->find();
			$_info['company_name'] = M('Company')->where(array('id'=>$_info['company']))->getField('name');
			$_info['cost_value'] = M('UserPackageCost')->where(array('custom_no'=>$_info['custom_no'],'type'=>10))->getField('cost_value');
			$this->assign('info', $_info);
			$has_package = M('UserPackage')->where(array('custom_no'=>$_info['custom_no'],'del'=>0))->field('package_sn,open_price,expense_price,service_start_time,service_end_time')->select();
			$package_mod = M('package');
			if($has_package)
			{
				$has_package_ids = array_unique(array_map(function($val) use ($key){return $val['package_sn'];},$has_package));
				$has_package_list = $package_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'package_sn'=>array('in',$has_package_ids)))->field('id,package_sn,name,carrieroperator')->select();
				foreach($has_package_list as $key=>$value)
				{
					foreach($has_package as $k=>$v)
					{
						if($value['package_sn'] == $v['package_sn'])
						{
							$has_package_list[$key]['service_start_time'] = $v['service_start_time'];
							$has_package_list[$key]['service_end_time'] = $v['service_end_time'];
							$has_package_list[$key]['open_price'] = $v['open_price'];
							$has_package_list[$key]['expense_price'] = $v['expense_price'];
						}
					}
				}
				$package_list = $package_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'package_sn'=>array('notin',$has_package_ids),'carrieroperator'=>$_info['carrieroperator']))->field('id,package_sn,name,carrieroperator')->select();
			}else{
				$has_package_list = array();
				$package_list = $package_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'carrieroperator'=>$_info['carrieroperator']))->field('id,package_sn,name,carrieroperator')->select();
			}
			$this->assign('has_package_list', $has_package_list);
			$this->assign('package_list', $package_list);
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
			$this->error('客户已删除！');
		}
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Custom', 'Custom', $id);
			$this->success('删除成功！');
		}
	}
}