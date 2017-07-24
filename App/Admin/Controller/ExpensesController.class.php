<?php 
/*
 * 续费管理
 */
 
namespace Admin\Controller;

class ExpensesController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		$this->Model = M('UserOrder');
    }
	
    /*续费列表
     **/
	public function index(){
		$map = array(
			'del' => 0,
			'deal_status' => 0,
			'service_type' => 11,
		);
		$get_data = I('get.');
		$_GET['search'] = '';
		$page_search = '';
		if(!empty($get_data['order_sn']))
		{
			$map['order_sn'] = array('like','%'.$get_data['order_sn'].'%');
			$_GET['search'] = 1;
			$page_search['order_sn'] = $get_data['order_sn'];
		}
		if(!empty($get_data['imsi']))
		{
			$map['imsi'] = array('like','%'.$get_data['imsi'].'%');
			$_GET['search'] = 1;
			$page_search['imsi'] = $get_data['imsi'];
		}
		if(!empty($get_data['pay_status']))
		{
			$map['pay_status'] = array('eq',$get_data['pay_status']);
			$_GET['search'] = 1;
			$page_search['pay_status'] = $get_data['pay_status'];
		}
		if(!empty($get_data['pay_type']))
		{
			$map['pay_type'] = array('eq',$get_data['pay_type']);
			$_GET['search'] = 1;
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		if(!empty($get_data['start']) && !empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array(array('egt',$get_data['start']),array('elt',$get_data['end']),'and');
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('elt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}elseif(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(create_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}
		$count      = $this->Model->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		
		$order_list = $this->Model->where($map)->field('id,order_sn,package_id,new_package_id,imsi,pay_type,service_type,pay_sn,pay_value,order_time,pay_status')->order('order_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$order_list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
	}
	
	public function view()
	{
		$_info=I('get.');
		$map = array(
			'id' => $_info['id'],
			'del' => 0,
		);
		$order_info = $this->Model->where($map)->field('order_sn,package_id,new_package_id,imsi,pay_type,service_type,pay_sn,pay_account,pay_value,order_time,pay_status')->find();
		$this->assign('order_info',$order_info);
		$custom_info = M('Custom')->where(array('imsi'=>$order_info['imsi']))->field('name,plate_number,carrieroperator')->find();
		$this->assign('custom_info',$custom_info);
		if($order_info['new_package_id'])
		{
			$package_id = $order_info['new_package_id'];
		}else{
			$package_id = $order_info['package_id'];
		}
		$package_info = M('Package')->where(array('package_sn'=>$package_id))->field('id,name')->find();
		$this->assign('package_info',$package_info);
		$this->display ();
	}
	
	//批量续费管理
	public function batch()
	{
		$map = array(
			'a.del' => 0,
		);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['package_name']))
		{
			$map['c.name'] = array('like','%'.$get_data['package_name'].'%');
			$_GET['search'] = 1;
			$page_search['package_name'] = $get_data['package_name'];
		}
		if(!empty($get_data['imsi']))
		{
			$map['b.imsi'] = array('like','%'.$get_data['imsi'].'%');
			$_GET['search'] = 1;
			$page_search['imsi'] = $get_data['imsi'];
		}
		$dbprefix = C('DB_PREFIX');
		$count      = M('UserBatchExpense')->table($dbprefix.'user_batch_expense as a')->join('left join '.$dbprefix.'custom as b ON a.imsi=b.imsi')->join('left join '.$dbprefix.'package as c ON a.package_sn=c.package_sn')->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$list = M('UserBatchExpense')->table($dbprefix.'user_batch_expense as a')->join('left join '.$dbprefix.'custom as b ON a.imsi=b.imsi')->join('left join '.$dbprefix.'package as c ON a.package_sn=c.package_sn')->where($map)->field('a.expense_time,a.renewal_order_sn,a.package_sn,a.create_time,b.imsi,b.name,b.plate_number,c.name as package_name,c.carrieroperator')->order('a.create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
	}
	//批量续费
	public function batchImport()
	{
		//VD标签判断
		if (!empty($_GET['Tag'])) {
			$_FILES['Tag'] = $_GET['Tag'];
		}
		//判断是否存在操作时间
		if (!empty($_POST['optime'])) {
			$_FILES['optime'] = $_POST['optime'];
		}
		if(IS_POST){
			if($this->deal_import_expenses($_FILES)){
                $this->success('导入成功');
            }else{
                $this->success('导入失败');
            }
		}else{
			$this->display ();
		}
	}


	//文件导入用户
    function deal_import_expenses($data)
    {
        $filename = $data['packageExpense']['tmp_name'];
        if (empty ($filename)) {
            echo '请选择要导入的XLS或XLSX文件！';
            exit;
        }
        $result = $this->import_excel($filename,$data['packageExpense']['name']); //解析
        $len_result = count($result);
        if($len_result==0){
            echo '没有任何数据！';
            exit;
        }
		$package_mod = M('Package');
		$custom_mod = M('Custom');
		$expenses_mod = M('UserBatchExpense');
		$user_package_mod = M('UserPackage');
		$log_mod = M('CustomHandleLog');
        $import_code = date('YmdHis-').rand(1000,9999);
        $fail = array();//失败手机号
        $ok = array();
		import('Common.Libs.AliyunhttpProducer');
		$producer = new \HttpProducer();
        for ($i = 0; $i < $len_result; $i++) 
		{
			$renewal_order_sn = iconv('gb2312', 'utf-8', $result[$i][0]) == false ? $result[$i][0] : iconv('gb2312', 'utf-8', $result[$i][0]); //续费的订单编号
            $imsi = iconv('gb2312', 'utf-8', $result[$i][1]) == false ? $result[$i][1] : iconv('gb2312', 'utf-8', $result[$i][1]); //中文转码
            $package_sn = iconv('gb2312', 'utf-8', $result[$i][2]) == false ? $result[$i][2] : iconv('gb2312', 'utf-8', $result[$i][2]);//套餐ID
            $price = iconv('gb2312', 'utf-8', $result[$i][4]) == false ? $result[$i][4] : iconv('gb2312', 'utf-8', $result[$i][4]);//套餐ID
            
            
			if(!$custom_info = $custom_mod->where(array('imsi'=>$imsi,'del'=>0,'status'=>1))->field('custom_no,card_number,tag,imsi,iccid,company')->find())
			{
                $fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '客户不存在';
                continue;
			}
			if(!$package_info = $package_mod->where(array('package_sn'=>$package_sn,'del'=>0,'status'=>1))->find())
			{
                $fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '套餐不存在';
                continue;
			}
			if(!$user_package_info = $user_package_mod->where(array('package_sn'=>$package_sn,'del'=>0,'custom_no'=>$custom_info['custom_no']))->field('service_start_time,service_end_time')->find())
			{
				$fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '用户没有绑定该套餐';
                continue;
			}
            $service_type = iconv('gb2312', 'utf-8', $result[$i][3]) == false  ? $result[$i][3]: iconv('gb2312', 'utf-8', $result[$i][3]);//变更方式
            
			if($service_type == '续费')
			{
				/*if($carrieroperator == '联通')
				{
					$carrieroperator = 10;
				}elseif($carrieroperator == '移动'){
					$carrieroperator = 11;
				}else{
					$carrieroperator = 12;
				}*/
				//判断是否有进行操作时间的修改
				if (!empty($data['optime'])) {

					$time = strtotime($data['optime']);
				}else{
					$time = time();
				}

				//计算套餐周期
				//当续费时间大于结束时间时
				if(date('Y-m-d') > date('Y-m-d',$user_package_info['service_end_time']))
				{

					$end_time = strtotime(date('Y-m',strtotime('+'.($package_info['cycle_value']-1).' month ',$user_package_info['service_end_time'])));
					$start_time = strtotime(date('Y-m',strtotime('+1 month ',$user_package_info['service_end_time'])));
					$start_time = strtotime(date('Y-m-d',$start_time). ' 00:00:01');//续费生效开始时间

				}else{
					$end_time = strtotime(date('Y-m',strtotime('+'.($package_info['cycle_value']).' month ',$user_package_info['service_end_time'])));
					$start_time = strtotime(date('Y-m-d'). ' 00:00:01');//续费生效开始时间
				}

				$year = date('Y',$end_time);
				$month = date('m',$end_time);
				$day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$end_time = strtotime($year.'-'.$month.'-'.$day. ' 23:59:59');//续费生效结束时间


				$temp = array(
					'sn' => $import_code,
					'renewal_order_sn'=>$renewal_order_sn,
					'imsi' => $imsi,
					'company' => $custom_info['company'],
					'package_sn' => $package_sn,
					'package_name' => $package_info['name'],
					'expense_time' => $package_info['cycle_value'],
					'carrieroperator' => $package_info['carrieroperator'],
					'order_money'=> $price,
					'service_type' => 10,
					'plate_number' => '',
					'user_name' => '',
					'create_time' => $time,
					'del' => 0,
				);
				$rs = $expenses_mod->add($temp);

				if($rs)
				{
					$rs = $user_package_mod->where(array('package_sn'=>$order_temp['new_package_id'],'custom_no'=>$custom_info['custom_no']))->save(array('service_end_time'=>$end_time,'expense_price'=>$price));
					if($rs === false)
					{
						$fail[$i]['imsi'] = $imsi;
						$fail[$i]['package_sn'] = $package_sn;
						$fail[$i]['reason'] = '系统未知原因';
						continue;
					}else{
						$ok[] = $imsi;
						$log = array(
							'type' => '11',
							'company' => $custom_info['company'],
							'custom_no' => $custom_info['custom_no'],
							'handle_type' => 10,
							'content' => $package_sn,
							'extra' => $package_sn,
							'valid_start_time'=>$start_time,
							'valid_end_time'=>$end_time,
							'sale_account'=>$price,
							'order_account'=>$price,
							'remark' => '套餐续费',
							'create_time' => $time,
						);
						$log_mod->add($log);

						//判断连接是否上传TAG=VD字段,不满足,则使用用户本身的Tag标签
						if ($data['Tag'] == 'VD') {
							$Tag = $data['Tag'];
						}else{
							$Tag = $custom_info['Tag'];
						}
						$data = array(
							'Version' => '1.0',
							'Tag' => $Tag,
							'OrderNo' => $renewal_order_sn,
							'OrderTime' => '',
							'PayType' => (int)2,
							'Money' => (double)0,
							'PayStaus' => (int)0,
							'PayNo' => '',
							'SIM' => $custom_info['card_number'],
							'IMSI' => $custom_info['imsi'],
							'ICCID' => $custom_info['iccid'],
							'DataPlanBefore' => array(
								'ID' => $package_info['package_sn'],
								'Name' => $package_info['name'],
								'Flow' => (double)$package_info['package_value'],
								'ServiceCycle' => (int)$package_info['cycle_value'],
								'BeginTime' => date('Y-m-d H:i:s',$user_package_info['service_start_time']),
								'EndTime' => date('Y-m-d H:i:s',$user_package_info['service_end_time']),
							),
							'DataPlanAfter' => array(
								'ID' => $package_info['package_sn'],
								'Name' => $package_info['name'],
								'Flow' => (double)$package_info['package_value'],
								'ServiceCycle' => (int)$package_info['cycle_value'],
								'BeginTime' => date('Y-m-d H:i:s',$user_package_info['service_start_time']),
								'EndTime' => date('Y-m-d H:i:s',$end_time),
								'valid_start_time'=>date('Y-m-d H:i:s',$start_time),
								'valid_end_time'=>date('Y-m-d H:i:s',$end_time),
								'sale_account'=>$price,
								'order_account'=>$price,
							)
						);
						$push = json_encode($data);
						$producer->process($push);
					}
				}else{
					$fail[$i]['imsi'] = $imsi;
					$fail[$i]['package_sn'] = $package_sn;
					$fail[$i]['reason'] = '系统原因';
					continue;
				}
			}
		}
		if(count($fail)>0)
		{
			$exts = explode('.',$data['packageExpense']['name']);
			$exts = $exts[count($exts)-1];
			$name = rtrim($data['packageExpense']['name'],'.'.$exts);
			$this->exportExcel($fail,$name.'失败表');
		}
		if(count($ok)>0)
		{
			return true;
		}else{
			return false;
		}
	}
	
/*	//文件导入用户 2016/9/1 删除旧版本
    function deal_import_expenses($data)
    {
        $filename = $data['packageExpense']['tmp_name'];
        if (empty ($filename)) {
            echo '请选择要导入的XLS或XLSX文件！';
            exit;
        }
        $result = $this->import_excel($filename,$data['packageExpense']['name']); //解析
        $len_result = count($result);
        if($len_result==0){
            echo '没有任何数据！';
            exit;
        }
       
		$package_mod = M('Package');
		$custom_mod = M('Custom');
		$expenses_mod = M('UserBatchExpense');
		$user_package_mod = M('UserPackage');
		$log_mod = M('CustomHandleLog');
        $import_code = date('YmdHis-').rand(1000,9999);
        $fail = array();//失败手机号
        $ok = array();
		import('Common.Libs.AliyunhttpProducer');
		$producer = new \HttpProducer();
        for ($i = 0; $i < $len_result; $i++) 
		{
            $imsi = iconv('gb2312', 'utf-8', $result[$i][0]) == false ? $result[$i][0] : iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码
            $package_sn = iconv('gb2312', 'utf-8', $result[$i][2]) == false ? $result[$i][2] : iconv('gb2312', 'utf-8', $result[$i][2]);//套餐ID
            
			if(!$custom_info = $custom_mod->where(array('imsi'=>$imsi,'del'=>0,'status'=>1))->field('custom_no,card_number,imsi,iccid,company')->find())
			{
                $fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '客户不存在';
                continue;
			}
			if(!$package_info = $package_mod->where(array('package_sn'=>$package_sn,'del'=>0,'status'=>1))->find())
			{
                $fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '套餐不存在';
                continue;
			}
			if(!$user_package_info = $user_package_mod->where(array('package_sn'=>$package_sn,'del'=>0,'custom_no'=>$custom_info['custom_no']))->field('service_start_time,service_end_time')->find())
			{
				$fail[$i]['imsi'] = $imsi;
                $fail[$i]['package_sn'] = $package_sn;
                $fail[$i]['reason'] = '用户没有绑定该套餐';
                continue;
			}
            $carrieroperator = iconv('gb2312', 'utf-8', $result[$i][1]) == false ? $result[$i][1]:iconv('gb2312', 'utf-8', $result[$i][1]);//运营商
            $package_name = iconv('gb2312', 'utf-8', $result[$i][3]) == false  ? $result[$i][3]:iconv('gb2312', 'utf-8', $result[$i][3]);
            $service_type = iconv('gb2312', 'utf-8', $result[$i][4]) == false  ? $result[$i][4]: iconv('gb2312', 'utf-8', $result[$i][4]);//变更方式
            $cycle_value = iconv('gb2312', 'utf-8', $result[$i][5]) == false  ? $result[$i][5]:iconv('gb2312', 'utf-8', $result[$i][5]);//续费周期
            $user_name = iconv('gb2312', 'utf-8', $result[$i][6]) == false  ? $result[$i][6]:iconv('gb2312', 'utf-8', $result[$i][6]);
            $plate_number = iconv('gb2312', 'utf-8', $result[$i][7]) == false  ? $result[$i][7]:iconv('gb2312', 'utf-8', $result[$i][7]);
			if($service_type == '续费')
			{
				if($carrieroperator == '联通')
				{
					$carrieroperator = 10;
				}elseif($carrieroperator == '移动'){
					$carrieroperator = 11;
				}else{
					$carrieroperator = 12;
				}
				$temp = array(
					'sn' => $import_code,
					'imsi' => $imsi,
					'package_sn' => $package_sn,
					'package_name' => $package_name,
					'expense_time' => $cycle_value,
					'carrieroperator' => $carrieroperator,
					'service_type' => 10,
					'plate_number' => $plate_number,
					'user_name' => $user_name,
					'create_time' => time(),
					'del' => 0,
				);
				$rs = $expenses_mod->add($temp);
				if($rs)
				{
					$time = date('Y-m',$user_package_info['service_end_time']);
					$end_time = strtotime(date('Y-m',strtotime('+'.$cycle_value.' month '.$time)));
					$rs = $user_package_mod->where(array('package_sn'=>$package_sn,'del'=>0,'custom_no'=>$custom_info['custom_no']))->save(array('service_end_time'=>$end_time));
					if($rs === false)
					{
						$fail[$i]['imsi'] = $imsi;
						$fail[$i]['package_sn'] = $package_sn;
						$fail[$i]['reason'] = '系统未知原因';
						continue;
					}else{
						$ok[] = $imsi;
						$log = array(
							'type' => '11',
							'company' => $custom_info['company'],
							'custom_no' => $custom_info['custom_no'],
							'handle_type' => 10,
							'content' => $package_sn,
							'extra' => $package_sn,
							'remark' => '套餐续费',
							'create_time' => time(),
						);
						$log_mod->add($log);
						
						$data = array(
							'Version' => '1.0',
							'Tag' => $package_info['tag'],
							'OrderNo' => '',
							'OrderTime' => '',
							'PayType' => (int)2,
							'Money' => (double)0,
							'PayStaus' => (int)0,
							'PayNo' => '',
							'SIM' => $custom_info['card_number'],
							'IMSI' => $custom_info['imsi'],
							'ICCID' => $custom_info['iccid'],
							'DataPlanBefore' => array(
								'ID' => $package_info['package_sn'],
								'Name' => $package_info['name'],
								'Flow' => (double)$package_info['package_value'],
								'ServiceCycle' => (int)$cycle_value,
								'BeginTime' => date('Y-m-d H:i:s',$user_package_info['service_start_time']),
								'EndTime' => date('Y-m-d H:i:s',$user_package_info['service_end_time']),
							),
							'DataPlanAfter' => array(
								'ID' => $package_info['package_sn'],
								'Name' => $package_info['name'],
								'Flow' => (double)$package_info['package_value'],
								'ServiceCycle' => (int)$cycle_value,
								'BeginTime' => date('Y-m-d H:i:s',$user_package_info['service_start_time']),
								'EndTime' => date('Y-m-d H:i:s',$end_time),
							)
						);
						$push = json_encode($data);
						$producer->process($push);
					}
				}else{
					$fail[$i]['imsi'] = $imsi;
					$fail[$i]['package_sn'] = $package_sn;
					$fail[$i]['reason'] = '系统原因';
					continue;
				}
			}
		}
		if(count($fail)>0)
		{
			$exts = explode('.',$data['packageExpense']['name']);
			$exts = $exts[count($exts)-1];
			$name = rtrim($data['packageExpense']['name'],'.'.$exts);
			$this->exportExcel($fail,$name.'失败表');
		}
		if(count($ok)>0)
		{
			return true;
		}else{
			return false;
		}
	}*/
	
	//读取excel
	function import_excel($temp_name,$name) {
		header("Content-type:text/html;charset=utf-8");
        import('Common.Libs.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $exts = explode('.',$name);
        $exts = $exts[count($exts)-1];
        //根据不同类型分别操作
        if($exts=='xlsx'||$exts=='xls' ){
            $objPHPExcel = \PHPExcel_IOFactory::load($temp_name);
        }else if( $exts=='csv1111111111111111' ){//todo csv暂未集成
        }else{
            $this->error('文件类型错误');
        }
        $sheet = $objPHPExcel->getSheet(0);

        //获取行数与列数,注意列数需要转换
        $highestRowNum = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnNum = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //取得字段，这里测试表格中的第一行为数据的字段，因此先取出用来作后面数组的键名
        $filed = array();
        for($i=0; $i<$highestColumnNum;$i++){
            $cellName = \PHPExcel_Cell::stringFromColumnIndex($i).'1';
            $cellVal = $sheet->getCell($cellName)->getValue();//取得列内容
            $filed []= $cellVal;
        }
        //开始取出数据并存入数组
        $data = array();
        $n = 0;
        for($i=2;$i<=$highestRowNum;$i++){
            for($j=0; $j<$highestColumnNum;$j++){
                $cellName = \PHPExcel_Cell::stringFromColumnIndex($j).$i;
                $cellVal = $sheet->getCell($cellName)->getValue();
                $out[$n][$j] = $cellVal;
            }
            $n++;
        }
        return $out;
    }
	
	/* 导出excel函数*/
    function exportExcel($data,$name='Excel')
	{
        import('Common.Libs.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setTitle("批量续费失败表")
            ->setSubject("批量续费失败表")
            ->setDescription("失败数据")
            ->setKeywords("excel")
            ->setCategory("result file");
        foreach($data as $k => $v){
            $num=$k+1;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, $v['imsi'])
                ->setCellValue('B'.$num, $v['package_sn'])
                ->setCellValue('C'.$num, $v['reason']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('批量续费失败数据表');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}