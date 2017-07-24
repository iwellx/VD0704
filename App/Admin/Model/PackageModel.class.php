<?php 
/*
 * 套餐管理模型
 */
 
namespace Admin\Model;
use Think\Model;

class PackageModel extends Model{

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '套餐名称不能为空！'),
        array('price', 'require', '套餐价格不能为空！'),
        array('card_from', 'require', '运营商编号不能为空！'),
        array('package_value', 'require', '套餐流量不能为空！'),
		//array('package_value', 'number', '套餐流量格式错误！'),
		array('price', 'number', '套餐价格格式错误！'),
        array('cycle_value', 'require', '套餐周期不能为空！'),
		array('cycle_value', 'number', '套餐周期格式错误！'),
        array('value', 'require', '流量周期不能为空！'),
		array('value', 'number', '流量周期格式错误！'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );

	public function delete($id=0)
	{
		$mod = M('Package');
		$pk = $mod->getPk();
		$result = $mod->where(array($pk=>$id,'del'=>0))->save(array('del'=>1));
		return $result;
	}
	
	public function deal_bind($data)
	{
		$mod = M('Package');
		$temp = $log = array();
		$sn = rand(100000,99999);
		$custom_info = M('custom')->where(array('custom_no'=>$data['custom_no']))->field('test_days,slience_days')->find();
		$free_time = $custom_info['test_days'] + $custom_info['slience_days'];
		$open_price ='';
		foreach($data['package'] as $key=>$value)
		{
			$package_info = $mod->where(array('id'=>$value,'del'=>0,'status'=>1))->field('package_sn,cycle_unit,cycle_value')->find();
			$open_price = $data['price'][$value];
			if($data['servce'][$value]['start'] && $data['servce'][$value]['end'])
			{
				$start_time = strtotime($data['servce'][$value]['start']);
				$end_time = strtotime($data['servce'][$value]['end']);
			}else{
				if($package_info)
				{
					if(empty($data['servce'][$value]['start']) && !empty($data['servce'][$value]['end']))
					{
						$start_time = date('Y-m-d');
						$end_time = $data['servce'][$value]['end'];
					}elseif(!empty($data['servce'][$value]['start']) && empty($data['servce'][$value]['end'])){
						$end_start = strtotime($data['servce'][$value]['start']);
						if($package_info['cycle_unit'] == 'year' )
						{
							$end_time = strtotime(date('Y',strtotime('+'.$package_info['cycle_value'].' year ',$end_start)));
						}elseif($package_info['cycle_unit'] == 'month' ){
							$end_time = strtotime(date('Y-m',strtotime('+'.$package_info['cycle_value'].' month ',$end_start)));
						}else{
							$end_time = strtotime(date('Y-m-d',strtotime('+'.$package_info['cycle_value'].' day ',$end_start)));
						}
					}else{
						$start_time = $end_start = strtotime(date('Y-m-d'));
						if($package_info['cycle_unit'] == 'year' )
						{
							$end_time = strtotime(date('Y',strtotime('+'.$package_info['cycle_value'].' year ',$end_start)));
						}elseif($package_info['cycle_unit'] == 'month' ){
							$end_time = strtotime(date('Y-m',strtotime('+'.$package_info['cycle_value'].' month ',$end_start)));
						}else{
							$end_time = strtotime(date('Y-m-d',strtotime('+'.$package_info['cycle_value'].' day ',$end_start)));
						}
					}
					$year_m = date('Y-m',$end_time);
					$day = date('t',$end_time);
					$end_time = strtotime($year_m.'-'.$day. ' 23:59:59');//续费生效结束时间
				}else{
					continue;
				}
			}
			$temp[] = array(
				'sn' => '',
				'custom_no' => $data['custom_no'],
				'package_sn' => $package_info['package_sn'],
				'open_price' => $open_price,
				'service_start_time' => '',//$start_time,
				'service_end_time' => '',//$end_time,
				'open_time' => $start_time,
				'del' => 0,
			);
			$log[] = array(
				'type' => '13',
				'custom_no' => $data['custom_no'],
				'handle_type' => 10,
				'content' => $package_info['package_sn'],
				'extra' => '',
				'remark' => '套餐销售',
				'create_time' => $start_time,
			);
		}
		$up_mod = M('UserPackage');
		$up_mod->where(array('custom_no'=>$data['custom_no']))->save(array('sn'=>$sn,'del'=>1));
		$up_mod->addAll($temp);
		M('CustomHandleLog')->addAll($log);
		$rs = '';
		if($data['type'] == 10)
		{
			$cost_mod = M('UserPackageCost');
			if($cost_mod->where(array('custom_no'=>$data['custom_no'],'type'=>$data['type']))->find())
			{
				$rs = $cost_mod->where(array('custom_no'=>$data['custom_no'],'type'=>$data['type']))->save(array('cost_value'=>$data['cost_value']));
			}else{
				$temp = array(
					'custom_no' => $data['custom_no'],
					'type' => $data['type'],
					'cost_value' => $data['cost_value'],
				);
				$rs = $cost_mod->add($temp);
			}
		}
		return $rs;
		exit;
	}
}