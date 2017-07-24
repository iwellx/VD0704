<?php 
/*
 * 订单管理
 */
 
namespace Admin\Controller;

class OrderController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		$this->Model = M('CustomHandleLog');
    }
	
    /*订单列表
     **/
	public function index(){
		$map = array();
		
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
/* 		if(!empty($get_data['order_sn']))
		{
			$map['a.order_sn'] = array('like','%'.$get_data['order_sn'].'%');
			$_GET['search'] = 1;
			$page_search['order_sn'] = $get_data['order_sn'];
		} */
		if(!empty($get_data['company_name']))
		{
			$map['b.unicom_name'] = array('like','%'.trim($get_data['company_name']).'%');
			$_GET['search'] = 1;
			$page_search['company_name'] = $get_data['company_name'];
		}
		if(!empty($get_data['imsi']))
		{
			$map['c.imsi'] = array('like','%'.trim($get_data['imsi']).'%');
			$_GET['search'] = 1;
			$page_search['imsi'] = $get_data['imsi'];
		}
		if(!empty($get_data['pay_type']))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
			$_GET['search'] = 1;
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		if(!empty($get_data['type']))
		{
			$map['a.type'] = array('eq',$get_data['type']);
			$_GET['search'] = 1;
			$page_search['type'] = $get_data['type'];
		}
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
		$db_pre = C('DB_PREFIX');
		$count      = $this->Model->join(' a left join '.$db_pre.'company b ON a.company=b.id')->join('left join '.$db_pre.'custom c ON a.custom_no=c.custom_no')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$order_list = $this->Model->join(' a left join '.$db_pre.'company b ON a.company=b.id')->join('left join '.$db_pre.'custom c ON a.custom_no=c.custom_no')->where($map)->field('a.id,a.type,a.custom_no,a.pay_type,a.order_account,a.create_time,b.unicom_name as company_name,c.imsi')->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$order_list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
	}
	/*导出
     **/
	public function export()
	{
		
		$map = array();
		$get_data = I('get.');
		if(!empty($get_data['company_name']))
		{
			$map['b.unicom_name'] = array('like','%'.trim($get_data['company_name']).'%');
		}
		if(!empty($get_data['imsi']))
		{
			$map['c.imsi'] = array('like','%'.trim($get_data['imsi']).'%');
		}
		if(!empty($get_data['pay_type']))
		{
			$map['a.pay_type'] = array('eq',$get_data['pay_type']);
		}
		if(!empty($get_data['type']))
		{
			$map['a.type'] = array('eq',$get_data['type']);
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$search_time = $get_data['start'].'至'.$get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$search_time = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.create_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$search_time = $get_data['start'];
		}
		$db_pre = C('DB_PREFIX');
		set_time_limit(0);
		$order_list = $this->Model->join(' a left join '.$db_pre.'company b ON a.company=b.id')->join('left join '.$db_pre.'custom c ON a.custom_no=c.custom_no')->join('LEFT JOIN '.$db_pre.'package d ON a.content= d.package_sn')->where($map)->field('a.id,a.type,a.custom_no,a.pay_type,a.order_account,a.create_time,b.unicom_name as company_name,c.imsi,a.content,d.name as package_name,d.cycle_value as package_cycle')->order('create_time desc')->select();
        import('Common.Libs.PHPExcel');
        $objPHPExcel = new \PHPExcel();
		$name = '平台订单'.$search_time.'报表';
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name)->mergeCells( 'A1:AJ1' )->setCellValue('A2','编号')->setCellValue('B2','用户编号')->setCellValue('C2','IMSI')->setCellValue('D2','所属公司')->setCellValue('E2','套餐名称')->setCellValue('F2','套餐周期')->setCellValue('G2','服务方式')->setCellValue('H2','支付方式')->setCellValue('I2','支付金额')->setCellValue('J2','订单时间');
		foreach($order_list as $key=>$value)
		{
			if($value['type'] == 10)
			{
				$service_type = '套餐开通';
			}elseif($value['type'] == 11){
				$service_type = '套餐续费';
			}elseif($value['type'] == 12){
				$service_type = '套餐更换';
			}else{
				$service_type = '套餐销售';
			}
			if($value['pay_type'] == 10)
			{
				$pay_type = '微信支付';
			}elseif($value['pay_type'] == 11){
				$pay_type = '支付宝';
			}else{
				$pay_type = '银行转账';
			}
			$time = date('Y-m-d H:i',$value['create_time']);
			$num=$key+3;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $key+1)->setCellValue('B'.$num, $value['custom_no'])->setCellValue('C'.$num, $value['imsi'])->setCellValue('D'.$num, $value['company_name'])->setCellValue('E'.$num, $value['package_name'])->setCellValue('F'.$num, $value['package_cycle'])->setCellValue('G'.$num, $service_type)->setCellValue('H'.$num, $pay_type)->setCellValue('I'.$num, $value['order_account'])->setCellValue('J'.$num, $time);
		}
        $objPHPExcel->getActiveSheet()->setTitle($name);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
		
	}
	
	public function view()
	{
		$_info=I('get.');
		$map = array(
			'a.id' => $_info['id'],
		);
		$db_pre = C('DB_PREFIX');
		$info = $this->Model->join(' a left join '.$db_pre.'company b ON a.company=b.id')->join('left join '.$db_pre.'custom c ON a.custom_no=c.custom_no')->where($map)->field('a.id,a.type,a.custom_no,a.pay_type,a.order_account,a.create_time,a.content as package_id,b.unicom_name as company_name,c.imsi,c.name,c.plate_number,c.carrieroperator')->find();
		if($info['type'] == 11)
		{
			$order_info = M('UserOrder')->where(array('imsi'=>$info['imsi'],'new_package_id'=>$info['package_id'],'order_time'=>$info['create_time']))->field('pay_sn,order_sn,pay_account')->find();
			$info['order_sn'] = $order_info['order_sn'];
			$info['pay_sn'] = $order_info['pay_sn'];
			$info['pay_account'] = $order_info['pay_account'];
		}
		$this->assign('order_info',$info);
		
		$package_info = M('Package')->where(array('package_sn'=>$info['package_id']))->field('id,name')->find();
		$this->assign('package_info',$package_info);
		$user_package = M('UserPackage')->where(array('package_sn'=>$package_id))->where(array('custom_no'=>$info['custom_no'],'package_sn'=>$info['package_id']))->field('service_start_time,service_end_time')->find();
		$this->assign('user_package',$user_package);
		$this->display ();
	}
}