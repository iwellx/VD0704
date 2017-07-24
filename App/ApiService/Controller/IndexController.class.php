<?php
namespace ApiService\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize()
	{
		
	}
	public function index()
	{

		print_r($info);exit;
	}
	
	public function deal_order()
	{
		$order_mod = M('UserOrder');
		$where['id'] = array('gt',177);
		$list = $order_mod->where(array('deal_status'=>0))->where($where)->select();
		$package_mod = M('Package');
		$user_package_mod = M('UserPackage');
		$log_mod = M('CustomHandleLog');
		$custom_mod = M('custom');
		$time = '2016-08-30 16:44';
		$where = array();
		$where['FROM_UNIXTIME(create_time, "%Y-%m-%d %H:%i")'] = array('elt',$time);
		foreach($list as $key=>$value)
		{
			$custom_info = $custom_mod->where(array('imsi'=>$value['imsi']))->field('custom_no,company')->find();
			if($custom_info){
				$package_info = $package_mod->where(array('package_sn'=>$value['new_package_id']))->find();
				$user_package_info = $user_package_mod->where(array('package_sn'=>$value['new_package_id'],'custom_no'=>$custom_info['custom_no']))->find();
				if($package_info)
				{
					$log_list = $log_mod->where(array('custom_no'=>$custom_info['custom_no'],'content'=>$value['new_package_id']))->where($where)->field('*,FROM_UNIXTIME(create_time, "%Y-%m") as valid_time')->order('type asc')->select();
					if($log_list)
					{
						$valid_times = array_column($log_list,'valid_time');
						foreach($log_list as $k=>$v)
						{
							if($k>0)
							{
								if($v['type']==10)
								{
									$log_mod->where(array('id'=>$v['id']))->save(array('type'=>11));
								}
							}else{
								continue;
							}
						}
						if(!in_array('2016-07',$valid_times) && !in_array('2016-08',$valid_times))
						{
							$log = array(
								'type' => 11,
								'company' => $custom_info['company'],
								'custom_no' => $custom_info['custom_no'],
								'handle_type' => 10,
								'extra' => $value['package_id'],//旧套餐
								'content' => $value['new_package_id'],
								'remark' => '套餐续费',
								'create_time' => $value['create_time'],
							);
							$log_mod->add($log);//日志记录
						}
					}else{
						$log = array(
							'type' => 11,
							'company' => $custom_info['company'],
							'custom_no' => $custom_info['custom_no'],
							'handle_type' => 10,
							'extra' => $value['package_id'],//旧套餐
							'content' => $value['new_package_id'],
							'remark' => '套餐续费',
							'create_time' => $value['create_time'],
						);
						$log_mod->add($log);//日志记录
					}
					if($value['service_type']==11)
					{
						$end_time = strtotime(date('Y-m',strtotime('+'.$package_info['cycle_value'].' month ',$user_package_info['service_end_time'])));
						$user_package_mod->where(array('package_sn'=>$value['new_package_id'],'custom_no'=>$custom_info['custom_no']))->save(array('service_end_time'=>$end_time));
					}else
					{
						$order_mod->where(array('id'=>$value['id']))->save(array('service_type'=>11));
					}
				}else{
					$order_mod->where(array('id'=>$value['id']))->save(array('deal_status'=>1,'deal_remark'=>'套餐不存在'));
				}
			}else{
				$order_mod->where(array('id'=>$value['id']))->save(array('deal_status'=>1,'deal_remark'=>'用户不存在'));
			}
		}
	}
	
	/**流量卡查询
	*/
    public function getDataInfo(){
		$sim = I('get.SIM');
		if(empty($sim))
		{
			
		}
		$_info = M('custom')->where(array('card_number'=>$sim,'del'=>0))->field('custom_no,tag,card_number,imsi,iccid')->find();
		//$_info['cost_value'] = M('UserPackageCost')->where(array('custom_no'=>$_info['custom_no'],'type'=>10))->getField('cost_value');
		$has_package = M('UserPackage')->where(array('custom_no'=>$_info['custom_no'],'del'=>0))->field('package_sn,service_start_time,service_end_time')->select();
		$package_mod = M('package');
		$DataPlan = array();
		if($has_package)
		{
			$has_package_ids = array_unique(array_map(function($val) use ($key){return $val['package_sn'];},$has_package));
			$has_package_list = $package_mod->where(array('status'=>1,'del'=>0,'tag'=>10,'package_sn'=>array('in',$has_package_ids)))->field('id,package_sn,name,price,package_value,cycle_unit,cycle_value,description')->select();
			foreach($has_package_list as $key=>$value)
			{
				if($value['cycle_unit'] == 'year')
				{
					$ServiceCycle = $value['cycle_value']*12;
				}else{
					$ServiceCycle = $value['cycle_value'];
				}
				$DataPlan[$key] = array(
					'ID' => $value['package_sn'],
					'Name' => $value['name'],
					'Price' => (double)$value['price'],
					'Flow' => (double)$value['package_value'],
					'Desc' => $value['description'],
					'ServiceCycle' => (int)$ServiceCycle,
				);
				foreach($has_package as $k=>$v)
				{
					if($value['package_sn'] == $v['package_sn'])
					{
						$DataPlan[$key]['BeginTime'] = date('Y-m-d H:i:s',$v['service_start_time']);
						$DataPlan[$key]['EndTime'] = date('Y-m-d H:i:s',$v['service_end_time']);
					}
				}
			}
			$DataPlan = array_values($DataPlan);
		}else{
			$has_package_list = array();
		}
		$temp = array(
			'Version' => '1.0',
			'Tag' => $_info['tag'],
			'SIM' => $_info['card_number'],
			'IMSI' => $_info['imsi'],
			'ICCID' => $_info['iccid'],
			'DataPlan' => $DataPlan,
		);
		$return = array(
			'success' => true,
			'Result' => $temp,
			'Message' => '',
			'SessionState' => true,
			'ExceptionMessage' => '',
		);
		echo $_GET['callback']."(".json_encode($return).")";exit;
    }
	
	/**获取套餐信息
	*/
	public function getPackageInfo()
	{
		$telecom = I('get.Telecom');
		if(empty($telecom))
		{
			
		}else{
			if($telecom == 'LT')
			{
				$carrieroperator = 10;
			}elseif($telecom == 'YD'){
				$carrieroperator = 11;
			}else{
				$carrieroperator = 12;
			}
		}
		$package_mod = M('package');
		$list = $package_mod->where(array('status'=>1,'del'=>0,'carrieroperator'=>$carrieroperator))->field('package_sn,name,price,package_value,unit,value,cycle_unit,cycle_value,description')->select();
		foreach($list as $key=>$value)
		{
			if($value['unit'] == 'year')
			{
				$FlowCycle = $value['value']*12;
			}else{
				$FlowCycle = $value['value'];
			}
			if($value['cycle_unit'] == 'year')
			{
				$ServiceCycle = $value['cycle_value']*12;
			}else{
				$ServiceCycle = $value['cycle_value'];
			}
			$temp[$key] = array(
				'Version' => '',
				'ID' => $value['package_sn'],
				'Name' => $value['name'],
				'Price' => (double)$value['price'],
				'Flow' => (double)$value['package_value'],
				'Telecom' => $telecom,
				'FlowCycle' => (int)$FlowCycle,
				'ServiceCycle' => (int)$ServiceCycle,
				'Desc' => $value['description'],
			);
		}
		$temp = array_values($temp);
		$return = array(
			'success' => true,
			'Result' => $temp,
			'Message' => '',
			'SessionState' => true,
			'ExceptionMessage' => '',
		);
		echo $_GET['callback']."(".json_encode($return).")";exit;
	}
}