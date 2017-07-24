<?php 
/*
 * 统计
 */
 
namespace Admin\Controller;

class CountsController extends AdminCoreController {
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		$this->Model = M('CustomHandleLog');
    }
    /*公司统计
     **/
	public function company(){
		$map = $map1 = $map2 =array(
			'del' => 0,
		);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = $count_search = '';
		$count_search['is_del'] = 0;
		if(!empty($get_data['company_name']))
		{
			$map['unicom_name'] = array('like','%'.$get_data['company_name'].'%');
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$end_time),array('elt',$get_data['end']));
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$end_time),array('elt',$get_data['end']));
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}else{
			$start_time = date('Y-m');
			$end_time = date('Y-m');
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$start_time),array('elt',$end_time));
		}
		
		$dbprefix = C('DB_PREFIX');
		$count      = M('company')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = M('company')->where($map)->field('id,unicom_name as name')->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('CompanyCounts');
		$db_pre = C('DB_PREFIX');
		foreach($list as $key=>$value)
		{
			/* $total = $counts_mod->where(array('is_del'=>0,'company_id'=>$value['id']))->field("new_user as total")->find();
			$count_search['company_id'] = $value['id']; 
			$new = $counts_mod->where($count_search)->field("SUM(new_user) as new")->find();
 			if($_GET['test']){
				
				print_r($total);echo $counts_mod->getlastsql() , ';';print_r($new);exit;
				
				
			}
			$expense = $counts_mod->where($count_search)->field("SUM(expense_user) as expense")->find();
			
			//本月数据
			$map2['company'] = array('eq',$value['id']);
			
			$map1['company'] = array('eq',$value['id']);
			
			//数据导入时，有可能因创建时间错乱，导致之前分期的用户新增数据错误
			$total_counts = $custom_mod->where($map1)->count('custom_no');
			
			$list[$key]['total_counts'] = $total['total']+$total_counts;
			
			$list[$key]['total_counts'] = $custom_mod->where(array('del'=>0,'company'=>array('eq',$value['id'])))->count('custom_no');
			
			$list[$key]['new_counts'] = $new['new']+$total_counts;
			
			$map2['custom_state'] = array('neq',13);
			$valid_counts = $custom_mod->where($map2)->count('custom_no');
			$valid_counts = $valid_counts>0?$valid_counts:0;
			$list[$key]['valid_counts'] = $valid_counts;
			
			
			
			$expense_counts = $custom_mod->table($db_pre.'custom_handle_log as a')->join('left join '.$db_pre.'custom as b on a.custom_no=b.custom_no')->where(array('a.type'=>11,'a.handle_type'=>10,"FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"=>$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"],'b.company'=>$value['id']))->field('b.custom_no')->count('a.custom_no');
			
			$expense_counts = $expense_counts>0?$expense_counts:0;
			$list[$key]['expense_counts'] = $expense['expense']+$expense_counts;
			
			unset($map1['type'],$map1['custom_no'],$map2['company'],$map2['custom_state'],$total); */
			
			$map2['company'] = array('eq',$value['id']);
			$list[$key]['total_counts'] = $custom_mod->where(array('del'=>0,'company'=>array('eq',$value['id'])))->count('custom_no');
			
			$list[$key]['new_counts'] = $custom_mod->where($map2)->count('custom_no');
			$map2['custom_state'] = array('eq',12);
			$list[$key]['valid_counts'] = $custom_mod->where($map2)->count('custom_no');
			unset($map2['custom_state']);
			$map2['type'] = array('eq',11);
			$map2['handle_type'] = array('eq',10);
			$list[$key]['expense_counts'] = $custom_handle_mod->where($map2)->count('distinct(custom_no)');
			/*分期统计
			$expense_cou nts = $custom_handle_mod->where($map2)->group("FROM_UNIXTIME(create_time, '%Y-%m')")->field('count(distinct(custom_no)) as month_expense')->select();
			$expense_all = array_map(function($val) use ($key){return $val['month_expense'];},$expense_counts);
			print_r(array_sum($expense_all));exit; */
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display ();
/* 		if(empty($get_data['company_name']))
		{
		}else{
			
			$this->assign('data',$list[0]);
			$this->display ('Counts/company_single');
		} */
	}
	

	public function companyExport(){
		$map = $map1 = $map2 =array(
			'del' => 0,
		);
		$get_data = I('get.');
		if(!empty($get_data['company_name']))
		{
			$map['unicom_name'] = array('like','%'.$get_data['company_name'].'%');
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$end_time),array('elt',$get_data['end']));
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
			$search_time = $get_data['end'].'至'.$get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$end_time),array('elt',$get_data['end']));
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$page_search['start'] = $get_data['start'];
			$search_time = '自'.$get_data['end'];
		}else{
			$start_time = date('Y-m');
			$end_time = date('Y-m');
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$start_time),array('elt',$end_time));
		}
		$list = M('company')->where($map)->field('id,unicom_name as name')->order('id asc')->select();
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('CompanyCounts');
		
        import('Common.Libs.PHPExcel');
        $objPHPExcel = new \PHPExcel();
		$name = $search_time.'公司统计报表';
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AE1' )->setCellValue('A2','企业名称')->setCellValue('B2','总用户数')->setCellValue('C2','新增用户数')->setCellValue('D2','续费用户数')->setCellValue('E2','有效用户数');
		foreach($list as $key=>$value)
		{
			$map2['company'] = array('eq',$value['id']);
			$total_counts = $custom_mod->where(array('del'=>0,'company'=>array('eq',$value['id'])))->count('custom_no');
			
			$new_counts = $custom_mod->where($map2)->count('custom_no');
			$map2['custom_state'] = array('eq',12);
			$valid_counts = $custom_mod->where($map2)->count('custom_no');
			unset($map2['custom_state']);
			$map2['type'] = array('eq',11);
			$map2['handle_type'] = array('eq',10);
			$expense_counts = $custom_handle_mod->where($map2)->count('distinct(custom_no)');
			
			$num=$key+3;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $value['name'])->setCellValue('B'.$num, $total_counts)->setCellValue('C'.$num, $new_counts)->setCellValue('D'.$num, $expense_counts)->setCellValue('E'.$num, $valid_counts);
		}
        $objPHPExcel->getActiveSheet()->setTitle($name);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
	
	
	public function company_counts(){
		$map = $map1 = array(
			'del' => 0,
		);
		$get_data = I('get.');
		$get_data['end'] = empty($get_data['end'])? date('Y-m-d'):$get_data['end'];
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map1["create_time"] = array(array('egt',strtotime($get_data['start'])),array('elt',strtotime($get_data['end'])),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map1["create_time"] = array('elt',strtotime($get_data['end']));
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map1["create_time"] = array('egt',strtotime($get_data['start']));
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		$dbprefix = C('DB_PREFIX');
		$list = M('company')->where($map)->field('id,unicom_name as name')->order('id desc')->select();
		
		unset($map['id']);
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$count_mod = M('CompanyCounts');
		foreach($list as $key=>$value)
		{
			$map1['company'] = array('eq',$value['id']);
			$custom_ids = $custom_mod->where($map1)->count();
			$add_counts = $custom_ids>0?$custom_ids:0;
			$map1['type'] = array('eq',10);
			$map1['handle_type'] = array('eq',10);
			
			$open_counts = $custom_handle_mod->where($map1)->count();
			$map1['type'] = array('eq',11);
		
			$expense_counts = $custom_handle_mod->where($map1)->count();
			$expense_counts = $expense_counts>0?$expense_counts:0;
			
			unset($map1['company']);
			unset($map1['type']);
			unset($map1['handle_type']);
			$temp = array(
				'type' => '10',
				'company_id' => $value['id'],
				'new_user' => $add_counts,
				'open_user' => $open_counts,
				'expense_user' => $expense_counts,
				'count_time' => date('Y-m',strtotime('-1 month')),
				'is_del' => 0,
			);
			$count_mod->where(array('company_id'=>$temp['company_id'],'count_time'=>$temp['count_time']))->save(array('is_del'=>1));
			$count_mod->add($temp);
			
		}
		
		
		
		echo 'OK';exit;
	}
	
	
/* 	public function companycount(){
		
		$map = $map1 = array(
			'del' => 0,
		);
		$get_data = I('get.');
		$get_data['end'] = '2016-06-30';
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map1["create_time"] = array(array('egt',strtotime($get_data['start'])),array('elt',strtotime($get_data['end'])),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map1["create_time"] = array('elt',strtotime($get_data['end']));
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map1["create_time"] = array('egt',strtotime($get_data['start']));
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		$dbprefix = C('DB_PREFIX');
		
		if(empty($_GET['company_id'])){
			
			$this->error("商户id不能为空！");
			
			die();
			
		}else{
			
			$map['id'] = array('in' , $_GET['company_id']);
			
		}
		
		
		
		$list = M('company')->where($map)->field('id,name')->order('id desc')->select();
		
		unset($map['id']);
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$count_mod = M('CompanyCounts');
		foreach($list as $key=>$value)
		{
			
			$map1['company'] = array('eq',$value['id']);
			$custom_ids = $custom_mod->where($map1)->group('imsi')->field('custom_no')->select();
			
			$add_counts = count($custom_ids)>0?count($custom_ids):0;
			
			unset($map1['company']);
			if($custom_ids)
			{
				$custom_ids = array_map(function($val) use ($key){return $val['custom_no'];},$custom_ids);
				
				$map1['custom_no'] = array('in',$custom_ids);
				$map1['type'] = array('eq',10);
				$map1['handle_type'] = array('eq',10);
				//$open_counts = $custom_handle_mod->where($map1)->field('id')->select();
				//$open_counts = count($open_counts)>0?count($open_counts):0;
				
				$open_counts = $custom_handle_mod->where($map1)->count();
				
				$map1['type'] = array('eq',11);
			
				$expense_counts = $custom_handle_mod->where($map1)->group('custom_no')->field('id')->select();
				$expense_counts = count($expense_counts)>0?count($expense_counts):0;
				
				//$expense_counts = $custom_handle_mod->where($map1)->group('custom_no')->count();
				
				//$subQuery = $custom_handle_mod->where($map1)->field('id')->group('custom_no')->select(false);
				
				//$expense_counts = $custom_handle_mod->table($subQuery.' a')->count() ;
				
				unset($map1['custom_no']);
				unset($map1['type']);
				unset($map1['handle_type']);
			}else{
				$expense_counts = 0;
			}
			$temp = array(
				'type' => '10',
				'company_id' => $value['id'],
				'new_user' => $add_counts,
				'open_user' => $open_counts,
				'expense_user' => $expense_counts,
				'count_time' => date('Y-m',strtotime($get_data['end'])),
				'is_del' => 0,
			);
			$count_mod->where(array('company_id'=>$temp['company_id'],'count_time'=>$temp['count_time']))->save(array('is_del'=>1));
			
			$count_mod->add($temp);
			
		}
		
		
		
		echo 'OK';exit;
	} */
	
	/**
		销售订单统计
	*/
	public function saleCounts()
	{
		$map = array('a.type'=>13);
		$get_data = I('get.');
		$_GET['search'] = '';
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		if($page_search)
		{
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->join('LEFT JOIN '.$dbprefix.'package c ON a.content = c.package_sn')->where($map)->group('a.company,a.content')->field('count(distinct a.custom_no) as total_customers,count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,b.unicom_name as company_name,c.name as package_name,c.package_sn,c.cycle_value as package_cycle')->select();
		//print_r($this->Model);exit;
		$this->assign('list',$list);
		$this->assign('page_search',$page_con);
		$this->display();
	}
	/**
		销售订单统计导出
	*/
	public function saleCountsExport()
	{
		$map = array('a.type'=>13);
		$get_data = I('get.');
		$_GET['search'] = '';
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',trim($get_data['company_name']));
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->join('LEFT JOIN '.$dbprefix.'package c ON a.content = c.package_sn')->where($map)->group('a.company,a.content')->field('count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,b.unicom_name as company_name,c.name as package_name,c.cycle_value as package_cycle')->select();
		if(!empty($list))
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'销售订单统计表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AF1' )->setCellValue('A2','编号')->setCellValue('B2','公司名称')->setCellValue('C2','套餐名称')->setCellValue('D2','套餐周期（月）')->setCellValue('E2','销售数量')->setCellValue('F2','销售总金额');
			$total=0;
			foreach($list as $key=>$value)
			{
				$total += $value['total_price'];
				$num=$key+3;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['company_name'])->setCellValue('C'.$num, $value['package_name'])->setCellValue('D'.$num, $value['package_cycle'])->setCellValue('E'.$num, $value['total_orders'])->setCellValue('F'.$num, $value['total_price']);
			}
			$num=$num+1;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num, '')->setCellValue('E'.$num,'' )->setCellValue('F'.$num,'总计：'.$total);
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	
	/*
		销售订单统计明细导出
	*/
	public function saleCountListExport()
	{
		$map = array('a.type'=>13);
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
		}
		if(!empty(trim($get_data['package_sn'])))
		{
			$map['a.content'] = array('eq',$get_data['package_sn']);
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and a.type=13')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'custom d ON a.custom_no= d.custom_no')->where($map)->order('a.create_time desc')->field('a.id,a.order_account,a.custom_no,a.pay_type,a.create_time,b.unicom_name as company_name,c.name as package_name,c.package_sn,c.cycle_value as package_cycle,d.imsi')->select();
		$custom_mod = M('Custom');
		if($list)
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'销售订单统计明细表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AG1' )->setCellValue('A2','编号')->setCellValue('B2','用户编号')->setCellValue('C2','IMSI')->setCellValue('D2','公司名称')->setCellValue('E2','套餐名称')->setCellValue('F2','套餐周期（月）')->setCellValue('G2','销售价格')->setCellValue('H2','销售时间');
			$total=0;
			foreach($list as $key=>$value)
			{
				$total += $value['order_account'];
				$time = date('Y-m-d',$value['create_time']);
				$num=$key+3;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['custom_no'])->setCellValue('C'.$num, $value['imsi'])->setCellValue('D'.$num, $value['company_name'])->setCellValue('E'.$num, $value['package_name'])->setCellValue('F'.$num, $value['package_cycle'])->setCellValue('G'.$num, $value['order_account'])->setCellValue('H'.$num, $time);
			}
			$num++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num,'')->setCellValue('E'.$num, '')->setCellValue('F'.$num,'')->setCellValue('G'.$num, '总计：'.$total)->setCellValue('H'.$num, '');
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	
	
	
	/*
		销售订单明细
	*/
	public function saleCountList()
	{
		$map = array('a.type'=>13);
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
			$page_search['company'] = $get_data['company'];
		}
		if(!empty(trim($get_data['package_sn'])))
		{
			$map['a.content'] = array('eq',$get_data['package_sn']);
			$page_search['package_sn'] = $get_data['package_sn'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		$dbprefix = C('DB_PREFIX');
		
		$count = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and a.type=13')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->where($map)->count();
		
		// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and a.type=13')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->where($map)->order('b.unicom_name asc')->field('a.id,a.order_account,a.custom_no,a.pay_type,a.create_time,b.unicom_name as company_name,c.name as package_name,c.package_sn,c.cycle_value as package_cycle')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('Custom');
		foreach($list as $key=>$value)
		{
			$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi')->find();
			$list[$key]['imsi'] = $info['imsi'];
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display();
	}
	
	
	
	
	/**
		续费订单统计
	*/
	public function expenseCounts()
	{
		$map = array('a.type'=>11);
		$get_data = I('get.');
		$_GET['search'] = '';
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$_GET['search'] = 1;
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		if($page_search)
		{
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$this->assign('page_search',$page_con);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->where($map)->group('a.company,a.pay_type')->field('count(distinct a.custom_no) as total_customers,count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,b.unicom_name as company_name')->select();
		$this->assign('list',$list);
		$this->display();
	}
	/**
		续费订单统计导出
	*/
	public function expenseCountsExport()
	{
		$map = array('a.type'=>11);
		$get_data = I('get.');
		$_GET['search'] = '';
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->where($map)->group('a.company,a.pay_type')->field('count(distinct a.custom_no) as total_customers,count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,b.unicom_name as company_name')->select();
		if(!empty($list))
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'续费订单统计表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AF1' )->setCellValue('A2','编号')->setCellValue('B2','公司名称')->setCellValue('C2','支付方式')->setCellValue('D2','续费人数')->setCellValue('E2','续费数量')->setCellValue('F2','续费总金额');
			$total=0;
			foreach($list as $key=>$value)
			{
				if($value['pay_type'] == 10)
				{
					$value['pay_type'] = '微信支付';
				}elseif($value['pay_type'] == 11){
					$value['pay_type'] = '支付宝';
				}else{
					$value['pay_type'] = '银行转账';
				}
				$total += $value['total_price'];
				$num=$key+3;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['company_name'])->setCellValue('C'.$num, $value['pay_type'])->setCellValue('D'.$num, $value['total_customers'])->setCellValue('E'.$num, $value['total_orders'])->setCellValue('F'.$num, $value['total_price']);
			}
			$num=$num+1;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num, '')->setCellValue('E'.$num,'' )->setCellValue('F'.$num,'总计：'.$total);
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	/*
		续费订单统计明细导出
	*/
	public function expenseCostListExport()
	{
		$map = array();
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
		}
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->order('b.unicom_name asc')->field('a.id,a.order_account,a.custom_no,a.pay_type,a.create_time,b.unicom_name as company_name,c.name as package_name,   c.package_sn,c.cycle_value as package_cycle,d.service_start_time,d.service_end_time')->select();
		$custom_mod = M('Custom');
		$user_order_mod = M('UserOrder');
		if($list)
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'续费订单统计明细表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AK1' )->setCellValue('A2','编号')->setCellValue('B2','用户编号')->setCellValue('C2','IMSI')->setCellValue('D2','公司名称')->setCellValue('E2','套餐名称')->setCellValue('F2','套餐周期（月）')->setCellValue('G2','支付方式')->setCellValue('H2','订单金额')->setCellValue('I2','订单编号')->setCellValue('J2','交易流水号')->setCellValue('K2','续费时间');
			
			$total=0;
			foreach($list as $key=>$value)
			{
				if($value['pay_type'] == 10)
				{
					$value['pay_type'] = '微信支付';
				}elseif($value['pay_type'] == 11){
					$value['pay_type'] = '支付宝';
				}else{
					$value['pay_type'] = '银行转账';
				}
				$total += $value['order_account'];
				$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi')->find();
				$value['imsi'] = $info['imsi'];
				
				$order_info = $user_order_mod->where(array('new_package_id'=>$value['package_sn'],'imsi'=>$info['imsi'],'order_time'=>$value['create_time']))->field('order_sn,pay_sn')->find();
				$value['order_sn'] = $order_info['order_sn'];
				$value['pay_sn'] = $order_info['pay_sn'];
				
				$num=$key+3;
				$time = date('Y-m-d',$value['create_time']);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['custom_no'])->setCellValue('C'.$num, $value['imsi'])->setCellValue('D'.$num, $value['company_name'])->setCellValue('E'.$num, $value['package_name'])->setCellValue('F'.$num, $value['package_cycle'])->setCellValue('G'.$num, $value['pay_type'])->setCellValue('H'.$num, $value['order_account'])->setCellValue('I'.$num, $value['order_sn'])->setCellValue('J'.$num, $value['pay_sn'])->setCellValue('K'.$num, $time);
			}
			$num++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num,'')->setCellValue('E'.$num, '')->setCellValue('F'.$num,'')->setCellValue('G'.$num, '')->setCellValue('H'.$num, '总计：'.$total)->setCellValue('I'.$num, '')->setCellValue('J'.$num, '')->setCellValue('K'.$num, '');
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	/*
		续费订单明细
	*/
	public function expenseCostList()
	{
		$map = array('a.type'=>11);
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
			$page_search['company'] = $get_data['company'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$page_search['start'] = $get_data['start'];
		}
		$dbprefix = C('DB_PREFIX');
		$count = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->order('b.unicom_name asc')->field('a.id,a.order_account,a.custom_no,a.pay_type,a.create_time,b.unicom_name as company_name,c.name as package_name,c.carrieroperator,c.package_sn,c.cycle_value as package_cycle,d.service_start_time,d.service_end_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('Custom');
		foreach($list as $key=>$value)
		{
			$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi,custom_state')->find();
			$list[$key]['imsi'] = $info['imsi'];
			$list[$key]['custom_state'] = $info['custom_state'];
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display();
	}
	/**
		续费率统计
	*/
	public function expenseRate(){
		$map = $map1 = array(
			'del' => 0,
		);
		$map2 = array();
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['company_name']))
		{
			$map['unicom_name'] = array('like','%'.$get_data['company_name'].'%');
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		//开通时间
		if(!empty($get_data['start_open']) && !empty($get_data['end_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$get_data['start_open']),array('elt',$get_data['end_open']),'and');
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start_open']),array('elt',$get_data['end_open']),'and');
			$_GET['search'] = 1;
			$page_search['start_open'] = $get_data['start_open'];
			$page_search['end_open'] = $get_data['end_open'];
		}elseif(!empty($get_data['end_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('elt',$get_data['end_open']);
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end_open']);
			$_GET['search'] = 1;
			$page_search['end_open'] = $get_data['end_open'];
		}elseif(!empty($get_data['start_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
			$_GET['search'] = 1;
			$page_search['start_open'] = $get_data['start_open'];
		}
		//续费时间
		if(!empty($get_data['start_expense']) && !empty($get_data['end_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array(array('egt',$get_data['start_expense']),array('elt',$get_data['end_expense']),'and');
			$_GET['search'] = 1;
			$page_search['start_expense'] = $get_data['start_expense'];
			$page_search['end_expense'] = $get_data['end_expense'];
		}elseif(!empty($get_data['end_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array('elt',$get_data['end_expense']);
			$_GET['search'] = 1;
			$page_search['end_expense'] = $get_data['end_expense'];
		}elseif(!empty($get_data['start_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
			$_GET['search'] = 1;
			$page_search['start_expense'] = $get_data['start_expense'];
		}
		$dbprefix = C('DB_PREFIX');
		$count      = M('company')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = M('company')->where($map)->field('id,unicom_name as name')->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('CompanyCounts');
		$db_prefix = C('DB_PREFIX');
		foreach($list as $key=>$value)
		{
			$map1['company'] = array('eq',$value['id']);
			$map1['handle_type'] = array('eq',10);
			$map1['type'] = array('eq',10);
			$open_total = $custom_handle_mod->where($map1)->count('custom_no');
			$list[$key]['open_counts'] = $open_total;
			
			$map2['a.company'] = array('eq',$value['id']);
			$map2['a.handle_type'] = array('eq',10);
			$map2['a.type'] = array('eq',10);
			$map2['b.company'] = array('eq',$value['id']);
			$map2['b.handle_type'] = array('eq',10);
			$map2['b.type'] = array('eq',11);
			$expense_total = $custom_handle_mod->table($db_prefix.'custom_handle_log a')->join('inner join '.$db_prefix.'custom_handle_log b ON a.custom_no=b.custom_no')->where($map2)->count('b.custom_no');//print_r($custom_handle_mod);exit;
			$list[$key]['expense_counts'] = $expense_total;
			$list[$key]['expense_rate'] = sprintf("%.2f",(($expense_total/$open_total)*100));
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display ();
	}
	
	
	public function expenseRateExport(){
		$map = $map1 = array(
			'del' => 0,
		);
		$map2 = array();
		$get_data = I('get.');
		if(!empty($get_data['company_name']))
		{
			$map['unicom_name'] = array('like','%'.$get_data['company_name'].'%');
		}
		//开通时间
		if(!empty($get_data['start_open']) && !empty($get_data['end_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$get_data['start_open']),array('elt',$get_data['end_open']),'and');
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array(array('egt',$get_data['start_open']),array('elt',$get_data['end_open']),'and');
		}elseif(!empty($get_data['end_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('elt',$get_data['end_open']);
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('elt',$get_data['end_open']);
		}elseif(!empty($get_data['start_open']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
			$map2["FROM_UNIXTIME(a.create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
		}
		//续费时间
		if(!empty($get_data['start_expense']) && !empty($get_data['end_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array(array('egt',$get_data['start_expense']),array('elt',$get_data['end_expense']),'and');
			$search_time = $get_data['start_open'].'至'.$get_data['end_expense'];
		}elseif(!empty($get_data['end_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array('elt',$get_data['end_expense']);
			$search_time = '截止'.$get_data['end_expense'];
		}elseif(!empty($get_data['start_expense']))
		{
			$map2["FROM_UNIXTIME(b.create_time, '%Y-%m')"] = array('egt',$get_data['start_open']);
			$search_time = '自'.$get_data['start_open'];
		}
		$list = M('company')->where($map)->field('id,unicom_name as name')->order('id asc')->select();
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('CompanyCounts');
		$db_prefix = C('DB_PREFIX');
		set_time_limit(0);
        import('Common.Libs.PHPExcel');
        $objPHPExcel = new \PHPExcel();
		$name = $search_time.'续费率统计报表';
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AD1' )->setCellValue('A2','企业名称')->setCellValue('B2','开通人数')->setCellValue('C2','续费次数')->setCellValue('D2','续费率');
		foreach($list as $key=>$value)
		{
			$map1['company'] = array('eq',$value['id']);
			$map1['handle_type'] = array('eq',10);
			$map1['type'] = array('eq',10);
			$open_total = $custom_handle_mod->where($map1)->count('custom_no');
			
			$map2['a.company'] = array('eq',$value['id']);
			$map2['a.handle_type'] = array('eq',10);
			$map2['a.type'] = array('eq',10);
			$map2['b.company'] = array('eq',$value['id']);
			$map2['b.handle_type'] = array('eq',10);
			$map2['b.type'] = array('eq',11);
			$expense_total = $custom_handle_mod->table($db_prefix.'custom_handle_log a')->join('inner join '.$db_prefix.'custom_handle_log b ON a.custom_no=b.custom_no')->where($map2)->count('b.custom_no');
			$expense_rate = sprintf("%.2f",(($expense_total/$open_total)*100));
			$expense_rate = $expense_rate>0 ? $expense_rate.'%' : 0;
			$num=$key+3;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $value['name'])->setCellValue('B'.$num, $open_total)->setCellValue('C'.$num, $expense_total)->setCellValue('D'.$num, $expense_rate);
		}
		
        $objPHPExcel->getActiveSheet()->setTitle($name);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
	
	public function orderCounts()
	{
		$map = array();
		$get_data = I('get.');
		$_GET['search'] = '';
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$_GET['search'] = 1;
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
			$_GET['search'] = 1;
			$page_search['type'] = $get_data['type'];
		}
		if($page_search)
		{
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join('a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->where($map)->group('a.company,a.pay_type,a.type')->field('count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,a.type,b.unicom_name as company_name')->select();
		$this->assign('list',$list);
		$this->assign('page_search',$page_con);
		$this->display();
	}
	/**
		订单统计导出
	*/
	public function orderCountsExport()
	{
		$map = array();
		$get_data = I('get.');
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start'])){
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
		}
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join('a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->where($map)->group('a.company,a.pay_type,a.type')->field('count(a.id) as total_orders,sum(a.order_account) as total_price,a.pay_type,a.company,a.type,b.unicom_name as company_name')->select();
		if(!empty($list))
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'订单统计表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AF1' )->setCellValue('A2','编号')->setCellValue('B2','公司名称')->setCellValue('C2','服务类型')->setCellValue('D2','订单总数')->setCellValue('E2','订单总金额')->setCellValue('F2','支付类型');
			$total=0;
			foreach($list as $key=>$value)
			{
				if($value['type'] == 10)
				{
					$value['type'] = '开通';
				}elseif($value['type'] == 11){
					$value['type'] = '续费';
				}elseif($value['type'] == 12){
					$value['type'] = '更改';
				}else{
					$value['type'] = '销售';
				}
				if($value['pay_type'] == 10)
				{
					$value['pay_type'] = '微信支付';
				}elseif($value['pay_type'] == 11){
					$value['pay_type'] = '支付宝';
				}else{
					$value['pay_type'] = '银行转账';
				}
				$total += $value['total_price'];
				$num=$key+3;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['company_name'])->setCellValue('C'.$num, $value['type'])->setCellValue('D'.$num, $value['total_orders'])->setCellValue('E'.$num, $value['total_price'])->setCellValue('F'.$num, $value['pay_type']);
			}
			$num=$num+1;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num, '')->setCellValue('E'.$num, '总计：'.$total)->setCellValue('F'.$num, '');
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	/*
		订单统计明细导出
	*/
	public function orderCostListExport()
	{
		$map = array();
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'] .'至'. $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$search_time = '截止'.$get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$search_time = '自'.$get_data['start'];
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
		}
		set_time_limit(0);
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->order('a.create_time asc')->field('a.custom_no,a.pay_type,a.create_time,a.order_account,a.type,a.company,a.content as package_id,c.name as package_name,c.cycle_value as package_cycle,b.unicom_name as company_name')->select();
		$custom_mod = M('Custom');
		$user_order_mod = M('UserOrder');
					
		if($list)
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = $search_time.'订单统计明细表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AK1' )->setCellValue('A2','编号')->setCellValue('B2','订单编号')->setCellValue('C2','IMSI')->setCellValue('D2','公司名称')->setCellValue('E2','服务类型')->setCellValue('F2','套餐名称')->setCellValue('G2','套餐周期（月）')->setCellValue('H2','支付方式')->setCellValue('I2','订单金额')->setCellValue('J2','交易流水号')->setCellValue('K2','订单时间');
			
			$total=0;
			foreach($list as $key=>$value)
			{
				$total += $value['order_account'];
				$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi')->find();
				$value['imsi'] = $info['imsi'];
				if($value['type']==11)
				{
					$order_info = $user_order_mod->where(array('new_package_id'=>$value['package_id'],'imsi'=>$info['imsi'],'order_time'=>$value['create_time']))->field('order_sn,pay_sn')->find();
					$value['order_sn'] = $order_info['order_sn'];
					$value['pay_sn'] = $order_info['pay_sn'];
				}
				if($value['type'] == 10)
				{
					$value['type'] = '开通';
				}elseif($value['type'] == 11){
					$value['type'] = '续费';
				}elseif($value['type'] == 12){
					$value['type'] = '更改';
				}else{
					$value['type'] = '销售';
				}
				if($value['pay_type'] == 10)
				{
					$value['pay_type'] = '微信支付';
				}elseif($value['pay_type'] == 11){
					$value['pay_type'] = '支付宝';
				}else{
					$value['pay_type'] = '银行转账';
				}
				
				$num=$key+3;
				$time = date('Y-m-d',$value['create_time']);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['order_sn'])->setCellValue('C'.$num, $value['imsi'])->setCellValue('D'.$num, $value['company_name'])->setCellValue('E'.$num, $value['type'])->setCellValue('F'.$num, $value['package_name'])->setCellValue('G'.$num, $value['package_cycle'])->setCellValue('H'.$num, $value['pay_type'])->setCellValue('I'.$num, $value['order_account'])->setCellValue('J'.$num, $value['pay_sn'])->setCellValue('K'.$num, $time);
			}
			$num=$num+1;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num,'')->setCellValue('E'.$num, '')->setCellValue('F'.$num,'')->setCellValue('G'.$num, '')->setCellValue('H'.$num, '')->setCellValue('I'.$num, '总计：'.$total)->setCellValue('J'.$num, '')->setCellValue('J'.$num, '');
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	
	/*
		订单统计明细
	*/
	public function orderCostList()
	{
		$map = array();
		$get_data = I('get.');
		if(!empty(trim($get_data['company'])))
		{
			$map['a.company'] = array('eq',$get_data['company']);
			$page_search['company'] = $get_data['company'];
		}
		//查询时间
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$page_search['start'] = $get_data['start'];
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
			$page_search['type'] = $get_data['type'];
		}
		if(!empty(trim($get_data['pay_type'])))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		$dbprefix = C('DB_PREFIX');
		$count = $this->Model->join('a LEFT JOIN '.$dbprefix.'company b ON a.company = b.id')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->order('a.create_time asc')->field('a.custom_no,a.pay_type,a.create_time,a.order_account,a.type,a.company,a.content as package_id,b.unicom_name as company_name')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('Custom');
		$user_order_mod = M('UserOrder');
		foreach($list as $key=>$value)
		{
			$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi,custom_state')->find();
			$list[$key]['imsi'] = $info['imsi'];
			$list[$key]['custom_state'] = $info['custom_state'];
			if($value['type']==11)
			{
				$order_info = $user_order_mod->where(array('new_package_id'=>$value['package_id'],'imsi'=>$info['imsi'],'order_time'=>$value['create_time']))->field('order_sn,pay_sn')->find();
				$list[$key]['order_sn'] = $order_info['order_sn'];
				$list[$key]['pay_sn'] = $order_info['pay_sn'];
			}
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display();
	}
	
	
	/**有效用户套餐缴费统计
	*/
	public function customCost()
	{
		$map = array();
		$map1 = array();
		$page_search = '';
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('eq',$get_data['company_name']);
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		//查询时间
		if(!empty($get_data['count_time']))
		{
			$_GET['search'] = 1;
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',$get_data['count_time']);
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',$get_data['count_time']);
			$page_search['count_time'] = $get_data['count_time'];
		}else{
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',date('Y-m'));
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',date('Y-m'));
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
			$_GET['search'] = 1;
			$page_search['type'] = $get_data['type'];
		}
		if($page_search)
		{
			$page_con = '&'.http_build_query($page_search);//跳转分页条件检索
		}
		$dbprefix = C('DB_PREFIX');
		/*$list = $this->Model->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn AND a.custom_no=d.custom_no')->where($map)->group('a.type,a.order_account,a.content')->order('b.unicom_name asc')->field('a.id,a.type,b.unicom_name as company_name,c.name as package_name,c.cycle_value,a.order_account as price,COUNT(a.id) as cost_counts,count(d.custom_no) as total_open,d.open_price,d.expense_price')->select();
		//print_r($this->Model);exit;
			foreach($list as $key=>$value)
			{
				if($value['type'] == 10)
				{
					$package_month_price = $value['open_price']/$value['cycle_value'];
					$list[$key]['package_month_price'] = sprintf("%.2f",$package_month_price);
					$list[$key]['total_price'] = sprintf("%.2f",($package_month_price*$value['total_open']));
					$list[$key]['cost_counts'] = $value['total_open'];
				}else{
					$package_month_price = $value['price']/$value['cycle_value'];
					$list[$key]['package_month_price'] = sprintf("%.2f",$package_month_price);
					$list[$key]['total_price'] = sprintf("%.2f",($package_month_price*$value['cost_counts']));
				}
			} 
		if($_GET['test']==1)
		{
			$list = $this->Model->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn AND a.custom_no=d.custom_no')->where($map)->group('a.type,a.order_account,a.content')->order('b.name asc')->field('a.id,a.type,b.name as company_name,c.name as package_name,c.cycle_value,a.order_account as price,COUNT(a.id) as cost_counts,d.open_price,d.expense_price')->select();
			foreach($list as $key=>$value)
			{
				if($value['type'] == 10)
				{
					$list[$key]['package_month_price'] = sprintf("%.2f",($value['open_price']/$value['cycle_value']));
					$list[$key]['total_price'] = sprintf("%.2f",($value['open_price']/$value['cycle_value']));
				}else{
					$package_month_price = $value['order_account']/$value['cycle_value'];
					$list[$key]['package_month_price'] = sprintf("%.2f",$package_month_price);
					$list[$key]['total_price'] = sprintf("%.2f",($package_month_price*$value['cost_counts']));
				}
			}
		}else{
			$list = $this->Model->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->where($map)->group('a.type,a.order_account,a.content')->order('b.name asc')->field('a.id,b.name as company_name,c.name as package_name,c.cycle_value,a.order_account as price,FORMAT((a.order_account/c.cycle_value),2) as package_month_price,COUNT(a.id) as cost_counts,FORMAT((a.order_account/c.cycle_value)*COUNT(a.id),2) as total_price,a.type')->select();
		} */
		$list = $this->Model->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->where($map)->group('a.type,a.content,a.company')->order('b.unicom_name asc,a.content asc')->field('a.id,a.company as company_id,b.unicom_name as company_name,c.name as package_name,c.package_sn,c.cycle_value,a.order_account as price,FORMAT((a.order_account/c.cycle_value),2) as package_month_price,COUNT(a.id) as cost_counts,FORMAT((a.order_account/c.cycle_value)*COUNT(a.id),2) as total_price,a.type')->select();
		$this->assign('list',$list);
		//print_r($this->Model);exit;
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->display();
	}
	
	/*
		月报表明细
	*/
	public function customCostList()
	{
		$get_data = I('get.');
		$map = array(
			'c.package_sn' => $get_data['package_sn'],
			'a.company' => $get_data['company'],
		);
		$page_search = array(
			'package_sn' => $get_data['package_sn'],
			'company' => $get_data['company'],
		);
		//查询时间
		if(!empty($get_data['count_time']))
		{
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',$get_data['count_time']);
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',$get_data['count_time']);
			$page_search['count_time'] = $get_data['count_time'];
		}else{
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',date('Y-m'));
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',date('Y-m'));
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
			$page_search['type'] = $get_data['type'];
		}
		$dbprefix = C('DB_PREFIX');
		$count      = M('custom_handle_log')->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = $this->Model->join(' a LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->join('LEFT JOIN '.$dbprefix.'user_package d ON a.content= d.package_sn and a.custom_no=d.custom_no')->where($map)->order('b.unicom_name asc')->field('a.id,a.custom_no,b.unicom_name as company_name,c.name as package_name,   c.package_sn,c.carrieroperator,d.service_start_time,d.service_end_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('Custom');
		foreach($list as $key=>$value)
		{
			$info = $custom_mod->where(array('custom_no'=>$value['custom_no']))->field('imsi,custom_state')->find();
			$list[$key]['imsi'] = $info['imsi'];
			$list[$key]['custom_state'] = $info['custom_state'];
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->assign('list',$list);
		$this->display();
	}
	
	
	/**
		导出
	*/
	public function customCostExport()
	{
		$map = array();
		$map1 = array();
		$get_data = I('get.');
		if(!empty(trim($get_data['company_name'])))
		{
			$map['b.unicom_name'] = array('like','%'.$get_data['company_name'].'%');
		}
		//查询时间
		if(!empty($get_data['count_time']))
		{
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',$get_data['count_time']);
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',$get_data['count_time']);
			$search_time = $get_data['count_time'];
		}else{
			$map["FROM_UNIXTIME(a.valid_start_time, '%Y-%m')"] = array('elt',date('Y-m'));
			$map["FROM_UNIXTIME(a.valid_end_time, '%Y-%m')"] = array('egt',date('Y-m'));
			$search_time = date('Y-m');
		}
		if(!empty(trim($get_data['type'])))
		{
			$map['a.type'] = array('eq',$get_data['type']);
		}
		$dbprefix = C('DB_PREFIX');
		$list = $this->Model->table($dbprefix.'custom_handle_log a')->join('LEFT JOIN '.$dbprefix.'company b ON a.company= b.id and a.handle_type=10 and (a.type=10 or a.type=11)')->join('LEFT JOIN '.$dbprefix.'package c ON a.content= c.package_sn')->where($map)->group('a.type,a.content,a.company')->order('b.unicom_name asc,a.content asc')->field('a.id,b.unicom_name as company_name,c.name as package_name,c.cycle_value,a.order_account as price,truncate((a.order_account/c.cycle_value),2) as package_month_price,COUNT(a.id) as cost_counts,truncate((a.order_account/c.cycle_value)*COUNT(a.id),2) as total_price,a.type')->select();
		if(!empty($list))
		{
			import('Common.Libs.PHPExcel');
			$objPHPExcel = new \PHPExcel();
			$name = '平台用户'.$search_time.'月报表';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AI1' )->setCellValue('A2','编号')->setCellValue('B2','公司名称')->setCellValue('C2','套餐名称')->setCellValue('D2','套餐周期（月）')->setCellValue('E2','套餐价格（月/周期）')->setCellValue('F2','套餐单价（元/月）')->setCellValue('G2','收费用户数')->setCellValue('H2','收费总额')->setCellValue('I2','收费类型');
			$total=0;
			foreach($list as $key=>$value)
			{
				if($value['type'] == 10)
				{
					$type = '首年';
					/* $package_month_price = $value['open_price']/$value['cycle_value'];
					$value['package_month_price'] = sprintf("%.2f",$package_month_price);
					$value['total_price'] = sprintf("%.2f",($package_month_price*$value['total_open']));
					$value['cost_counts'] = $value['total_open']; */
				}else{
					$type = '非首年';
					/* $package_month_price = $value['order_account']/$value['cycle_value'];
					$value['package_month_price'] = sprintf("%.2f",$package_month_price);
					$value['total_price'] = sprintf("%.2f",($package_month_price*$value['cost_counts'])); */
				}/* 
				$package_month_price = $value['order_account']/$value['cycle_value'];
				$value['package_month_price'] = sprintf("%.2f",$package_month_price);
				$value['total_price'] = sprintf("%.2f",($package_month_price*$value['cost_counts'])); */
				$total += $value['total_price'];
				$num=$key+3;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['company_name'])->setCellValue('C'.$num, $value['package_name'])->setCellValue('D'.$num, $value['cycle_value'])->setCellValue('E'.$num, $value['price'])->setCellValue('F'.$num, $value['package_month_price'])->setCellValue('G'.$num, $value['cost_counts'])->setCellValue('H'.$num, $value['total_price'])->setCellValue('I'.$num, $type);
			}
			$num=$num+1;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, '')->setCellValue('C'.$num, '')->setCellValue('D'.$num, '')->setCellValue('E'.$num, '')->setCellValue('F'.$num, '')->setCellValue('G'.$num, '')->setCellValue('H'.$num, '总计：'.$total)->setCellValue('I'.$num, '');
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			$this->error('没有任何导出数据，无法导出',U('counts/customCost'));
		}
	}
	
	
	public function test()
	{
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('PackageCounts');
		$start_time = $custom_handle_mod->order('create_time asc')->getField('create_time');
		$start_time = date('Y-m',$start_time).'-01';
		$end_time = $custom_handle_mod->order('create_time desc')->getField('create_time');
		if(date('Y-m',$end_time) >= date('Y-m'))
		{
			$end_time = date('Y-m',strtotime('-1 month')).'-31';
		}else{
			$end_time = date('Y-m',$end_time).'-31';
		}
		$info = $this->diffDate($start_time,$end_time);
		$total_month = $info['year']*12+$info['month'];
		set_time_limit(0);
		for($i=0;$i<=$total_month;$i++)
		{
			$data = array();
			$begin = date('Y-m-d',strtotime('+'.$i.' month '.$start_time));
			$end = date('Y-m',strtotime($begin)).'-31';
			$data[] = $this->deal_test($begin,$end);print_r($data);
			//$counts_mod->addAll($temp);
		}exit;
	}
	
	public function deal_test($begin,$end)
	{
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('PackageCounts');
		$map = $map1 = array(
			'del' => 0,
		);
		if(!empty($begin) && !empty($end))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array(array('egt',$begin),array('elt',$end),'and');
		}elseif(!empty($get_data['end']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('elt',$end);
		}elseif(!empty($get_data['start']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('egt',$begin);
		}
		$list = M('company')->where($map)->field('id,name')->order('id desc')->select();
		$data = array();
		foreach($list as $key=>$value)
		{
			$map1['company'] = array('eq',$value['id']);
			$custom_ids = $custom_mod->where($map1)->count();
			$add_counts = $custom_ids>0?$custom_ids:0;
			
			$map1['type'] = array('eq',10);
			$map1['handle_type'] = array('eq',10);
			$open_counts = $custom_handle_mod->where($map1)->count();
			
			$map1['type'] = array('eq',11);
			$expense_counts = $custom_handle_mod->where($map1)->count('distinct(custom_no)');
			
			unset($map1['company']);
			unset($map1['type']);
			unset($map1['handle_type']);
			$temp = array(
				'type' => '10',
				'company_id' => $value['id'],
				'new_user' => $add_counts,
				'open_user' => $open_counts,
				'expense_user' => $expense_counts,
				'count_time' => date('Y-m',strtotime($begin)),
				'is_del' => 0,
			);
			$data[] = $temp;
			$counts_mod->where(array('company_id'=>$temp['company_id'],'count_time'=>$temp['count_time']))->save(array('is_del'=>1));
		}
		$counts_mod->addAll($data);
		return $temp;
	}
	
	public function company_index()
	{
		//分期统计时，续费人数问题：当一个用户在上个月有充值时，又在这个月充值，时间区间检索时，算二次，但实际情况是一次
		$map = $map1 = $map2 =array(
			'del' => 0,
		);
		$get_data = I('get.');
		$get_data['start'] = '2016-05-01';
		$get_data['end'] = '2016-06-01';
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = $count_search = '';
		$count_search['is_del'] = 0;
		if(!empty($get_data['company_name']))
		{
			$map['name'] = array('like','%'.$get_data['company_name'].'%');
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$start_time = date('Y-m',strtotime($get_data['start']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$start_time),array('elt',$end_time),'and');
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',$start_time),array('elt',$end_time),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
			
			//分期统计时间
			$search_end_time = $end_time;
			if($search_end_time>=date('Y-m'))
			{
				$count_end_time = date('Y-m',strtotime('-'.$search_end_time.' month'));
			}else{
				$count_end_time = $search_end_time;
			}
			$count_start_time = date('Y-m',strtotime($get_data['start']));
			$count_search['count_time']  = array(array('egt',$count_start_time),array('elt',$count_end_time),'and');//分期统计时间
		}elseif(!empty($get_data['end']))
		{
			$end_time = date('Y-m',strtotime($get_data['end']));
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array(array('egt',date('Y-m')),array('elt',$end_time),'and');//当前月统计时间
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
			
			//分期统计时间
			$search_end_time = date('Y-m',strtotime($get_data['end']));
			if($search_end_time>=date('Y-m'))
			{
				$count_end_time = date('Y-m',strtotime('-1 month'));
			}else{
				$count_end_time = $search_end_time;
			}
			$count_search['count_time'] = array('elt',$count_end_time);
		}elseif(!empty($get_data['start']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$map2["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			
			$count_start_time = date('Y-m',strtotime($get_data['start']));
			$count_search['count_time'] = array('egt',$count_start_time);
		}else{
			$start_time = date('Y-m').'-01';
			$end_time = date('Y-m').'-31';
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array(array('egt',$start_time),array('elt',$end_time),'and');//分期统计时间
			
			//分期统计时间
			$search_end_time = date('Y-m',strtotime($end_time));
			if($search_end_time>=date('Y-m'))
			{
				$count_end_time = date('Y-m',strtotime('-1 month'));
			}else{
				$count_end_time = $search_end_time;
			}
			$count_search['count_time'] = array('elt',$count_end_time);
		}
		$count      = M('company')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = M('company')->where($map)->field('id,name')->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('custom');
		$custom_handle_mod = $this->Model;
		$counts_mod = M('PackageCounts');
		foreach($list as $key=>$value)
		{
			//已分期数据
			$total = $counts_mod->where(array('is_del'=>0,'company_id'=>$value['id']))->field("SUM(new_user) as total")->find();
			$new = $expense = array();
			if(date('Y-m',strtotime($get_data['start'])) < date('Y-m'))
			{
				$count_search['company_id'] = $value['id']; 
				$new = $counts_mod->where($count_search)->field("SUM(new_user) as new")->find();
				$expense = $counts_mod->where($count_search)->field("SUM(expense_user) as expense")->find();
			}
			if(date('Y-m',strtotime($end_time)) >= date('Y-m') || empty($get_data['end']))
			{
				//未分期数据
				$map1['company'] = array('eq',$value['id']);
				$total_counts = $custom_mod->where($map1)->count('custom_no');//本月新加用户
				$list[$key]['total_counts'] = $total['total']+$total_counts;
				$list[$key]['new_counts'] = $new['new']+$total_counts;
				
				$map1['type'] = array('eq',11);
				$map1['handle_type'] = array('eq',10);
				$expense_counts = $custom_handle_mod->where($map1)->count('distinct(custom_no)');
				$list[$key]['expense_counts'] = $expense['expense']+$expense_counts;
			}else{
				$list[$key]['total_counts'] = $total['total'];
				$list[$key]['new_counts'] = $new['new'];
				$list[$key]['expense_counts'] = $expense['expense'];
			}
			unset($map1['type'],$map1['handle_type']);
			$map2['company'] = array('eq',$value['id']);
			$map2['custom_state'] = array('eq',12);
			$list[$key]['valid_counts'] = $custom_mod->where($map2)->count('custom_no');
			unset($map1['custom_state']);
		}
		
		$this->assign('list',$list);
		$this->display ('Counts/company');
	}
	
	/* 
    *function：计算两个日期相隔多少年，多少月，多少天 
    *param string $start[格式如：2011-11-5] 
    *param string $end[格式如：2012-12-01] 
    *return array array('年','月','日'); 
    */ 
    function diffDate($date1,$date2){ 
        if(strtotime($date1)>strtotime($date2)){ 
			$tmp=$date2; 
			$date2=$date1; 
			$date1=$tmp; 
		} 
		list($Y1,$m1,$d1)=explode('-',$date1); 
		list($Y2,$m2,$d2)=explode('-',$date2); 
		$Y=$Y2-$Y1; 
		$m=$m2-$m1; 
		$d=$d2-$d1; 
		if($d<0){ 
			$d+=(int)date('t',strtotime("-1 month $date2")); 
			$m--; 
		} 
		if($m<0){ 
			$m+=12; 
			$y--; 
		} 
		return array('year'=>$Y,'month'=>$m,'day'=>$d); 
    } 

	/**矩阵统计demo
	*/
/* 	public function expenseRate()
	{
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		if($get_data['start'])
		{
			$_GET['search'] = 1;
		}
		if(!empty($get_data['package_name']))
		{
			$sn = M('package')->where(array('package_name'=>$get_data['package_name']))->getField('package_sn');
			$get_data['package_sn'] = $sn;
			$_GET['search'] = 1;
		}
		$start = empty($get_data['start']) ? date('Y-m-d') : $get_data['start'];
		$cur_month = trim(date('m',strtotime($start)),'0');
		$thead = $tbody = '';
		$month_word = array(
			'1' => '一月',
			'2' => '二月',
			'3' => '三月',
			'4' => '四月',
			'5' => '五月',
			'6' => '六月',
			'7' => '七月',
			'8' => '八月',
			'9' => '九月',
			'10' => '十月',
			'11' => '十一月',
			'12' => '十二月',
		);
		$thead .= '<thead>';
		$tbody .= '<tbody>';
		for($x=0;$x<13;$x++)
		{
			if($x==0)
			{
				$thead .= '<tr><th></th>';
			}elseif($x==12)
			{
				if($cur_month == 12)
				{
					$thead .= '<th  class="red">'.$month_word[$x].'</th></tr>';
				}else{
					$thead .= '<th>'.$month_word[$x].'</th></tr>';
				}
			}else{
				if($cur_month == $x)
				{
					$thead .= '<th class="red">'.$month_word[$x].'</th>';
				}else{
					$thead .= '<th>'.$month_word[$x].'</th>';
				}
			}
			if($x>0)
			{
				
				for($y=0;$y<13;$y++)
				{	
					if($y == 0)
					{
						$tbody .= '<tr>';
						if($cur_month == $x)
						{
							$tbody .= '<td class="font-bold red">'.$month_word[$x].'</td>';
						}else{
							
							$tbody .= '<td class="font-bold">'.$month_word[$x].'</td>';
						}
					}elseif($y == 12)
					{
						$tbody .= '<td></td>';
						$tbody .= '</tr>';
					}else{
						$tbody .= '<td></td>';
					}
					
				}
			}
			
		}
		$thead .= '</thead>';
		$tbody .= '</tbody>';
		$this->assign('thead',$thead);
		$this->assign('tbody',$tbody);
		$this->assign('is_loading',0);
		$this->display ();
	}
	
	private function getExpenseRate($x,$y,$current,$time,$get_data='')
	{
		$cur_year = date('Y',strtotime($time));
		if(date('Y') > $cur_year)
		{
			$current = 13;
		}
		$custom_handle_mod = $this->Model;
		$custom_mod = M('Custom');
		$map = array();
		if($x+$y <= $current)
		{
			$rate_time = strtotime($cur_year.'-'.($x+$y-1));
			$time = date('Y-m',$rate_time);
			$map["FROM_UNIXTIME(create_time, '%Y-%m')"] = array('eq',$time);
			$add_counts = $custom_mod->where($map)->group('custom_no')->field('id')->select();
			$add_counts = count($add_counts)>0?count($add_counts):0;
			
			$map['type'] = array('eq',11);
			if($get_data['package_sn'])
			{
				$map['content'] = array('eq',$get_data['package_sn']);
			}
			$expense_counts = $custom_handle_mod->where($map1)->field('id')->select();
			$expense_counts = count($expense_counts)>0?count($expense_counts):0;
			
			if($expense_counts>0 && $add_counts>0)
			{
				$rate = ($expense_counts/$add_counts)*100;
				return $rate.'%';
			}else{
				return '0';
			}
		}else{
			return '';
		}
	} */
	
	
	/*公司统计
     **/
	public function package(){
		$map = $map1 = array(
			'del' => 0,
		);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['package_name']))
		{
			$map['name'] = array('like','%'.$get_data['package_name'].'%');
			$_GET['search'] = 1;
			$page_search['package_name'] = $get_data['package_name'];
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map1["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		$dbprefix = C('DB_PREFIX');
		$count      = M('package')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = M('package')->where($map)->field('id,package_sn,name')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$custom_mod = M('custom');
		$custom_package_mod = M('UserPackage');
		$custom_handle_mod = $this->Model;
		foreach($list as $key=>$value)
		{
			$map2 = array();
			$map2['name'] = $map1['name'];
			$map2['package_sn'] = array('eq',$value['package_sn']);
			$custom_ids = $custom_package_mod->where($map2)->group('custom_no')->field('custom_no')->select();
			if($custom_ids)
			{
				$map1['content'] = array('eq',$value['package_sn']);
				
				$map1['type'] = array('eq',10);
				$add_counts = $custom_handle_mod->where($map1)->group('custom_no')->field('id')->select();
				$add_counts = count($add_counts)>0?count($add_counts):0;
				
				$map1['type'] = array('eq',11);
				$expense_counts = $custom_handle_mod->where($map1)->group('custom_no')->field('id')->select();
				$expense_counts = count($expense_counts)>0?count($expense_counts):0;
				
				
				unset($map1['type']);
				$custom_ids = array_map(function($val) use ($key){return $val['custom_no'];},$custom_ids);
				$map1['custom_no'] = array('in',$custom_ids);
				$package_counts = $custom_handle_mod->where($map1)->group('custom_no')->field('custom_no')->select();
				$custom_ids = array_map(function($val) use ($key){return $val['custom_no'];},$package_counts);
				if($custom_ids)
				{
					$map1['custom_no'] = array('in',$custom_ids);
					$map1['custom_state'] = array('neq',13);
					unset($map1['content']);
					$valid_counts = $custom_mod->where($map1)->group('custom_no')->field('id')->select();
					$valid_counts = count($valid_counts)>0?count($valid_counts):0;
				}else{
					$valid_counts = 0;
				}
				
				unset($map1['custom_state']);
			}else{
				$add_counts = 0;
				$expense_counts = 0;
				$valid_counts = 0;
			}
			$list[$key]['add_counts'] = $add_counts;
			$list[$key]['valid_counts'] = $valid_counts;
			$list[$key]['expense_counts'] = $expense_counts;
		}
		$this->assign('list',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
	}
	
	
	
	
	
	
	
	public function getPackageList()
	{
		$p = I('get.p');
		$map = array(
			'del' => 0
		);
		if($p)
		{
			$map['name'] = array('LIKE','%'.$p.'%');
		}
		$temp = array();
		$list = M('package')->where($map)->order('create_time desc')->field('package_sn,name')->select();
		foreach($list as $key=>$value)
		{
			$temp['result'][$key][] = $value['package_sn'];
			$temp['result'][$key][] = $value['name'];
		}
		$temp = json_encode($temp);
		echo $temp;exit;
	}
	
	
	public function getCompanyList()
	{
		$p = I('get.p');
		$map = array(
			'del' => 0
		);
		if($p)
		{
			$map['unicom_name'] = array('LIKE','%'.$p.'%');
		}
		$temp = array();
		$list = M('company')->where($map)->order('create_time desc')->field('unicom_name as name')->select();
		foreach($list as $key=>$value)
		{
			$temp['result'][$key][] = $value['name'];
		}
		$temp = json_encode($temp);
		echo $temp;exit;
	}
}