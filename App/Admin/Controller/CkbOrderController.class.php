<?php
namespace Admin\Controller;
use Think\Controller;
class CkbOrderController extends Controller {
	
	public function deal_ckb_order()
	{
		$message = I('post.');
		$data = json_decode(base64_decode($message['data']),true);
		$order_mod = M('UserOrder');
		if($order_mod->where(array('order_sn'=>$data['OrderNo']))->count()>0)
		{//重复订单过滤
			$this->ajaxReturn(array('code'=>200));exit;
		}
		$pay_type = 10;
		if($data['PayType'] == 2)
		{
			$pay_type = 11;
		}elseif($data['PayType'] == 3){
			$pay_type = 12;
		}
		$pay_status = 10;
		if($data['PayStaus'] == 1)
		{
			$pay_status = 11;
		}
		if($data['DataPlanAfter']['ID'] && $data['DataPlanBefore']['ID'])
		{
			if($data['DataPlanAfter']['ID'] == $data['DataPlanBefore']['ID'])
			{
				$service_type = '11';//续费
			}else{
				$service_type = '12';//更换
			}
		}else{
			$service_type =  '10';//开通
		}
		$order_temp = array(
			'order_sn' => $data['OrderNo'],
			'package_id' => $data['DataPlanAfter']['ID'],
			'new_package_id' => $data['DataPlanBefore']['ID'],
			'imsi' => $data['IMSI'],
			'pay_type' => $pay_type,
			'service_type' => $service_type,
			'pay_sn' => $data['PayNo'],
			'pay_account' => '',
			'pay_value' => $data['Money']/100,
			'order_time' => strtotime($data['OrderTime']),
			'valid_start_time' => strtotime($data['DataPlanAfter']['valid_start_time']),
			'valid_end_time' => strtotime($data['DataPlanAfter']['valid_end_time']),
			'pay_status' => $pay_status,
			'create_time' => time(),
			'del' => 0,
		);
		$custom_info = M('custom')->where(array('imsi'=>$data['IMSI']))->field('custom_no,company')->find();
		$order_temp['company'] = $custom_info['company'];
		$rs = $order_mod->add($order_temp);
		if($rs)
		{
			if($custom_info)
			{
				$package_mod = M('UserPackage');
				$package_info = $package_mod->where(array('package_sn'=>$order_temp['new_package_id'],'custom_no'=>$custom_info['custom_no']))->find();
				if($package_info)
				{
					$extra = $remark = '';
					if($service_type == 11)
					{
						$package_mod->where(array('package_sn'=>$order_temp['new_package_id'],'custom_no'=>$custom_info['custom_no']))->save(array('service_end_time'=>strtotime($data['DataPlanAfter']['EndTime']),'expense_price'=>$data['DataPlanAfter']['sale_account']));
						$remark = '套餐续费';
					}
					$log = array(
						'type' => $service_type,
						'company' => $custom_info['company'],
						'custom_no' => $custom_info['custom_no'],
						'handle_type' => 10,
						'extra' => $data['DataPlanBefore']['ID'],//旧套餐
						'content' => $data['DataPlanAfter']['ID'],
						'valid_start_time' => strtotime($data['DataPlanAfter']['valid_start_time']),
						'valid_end_time' => strtotime($data['DataPlanAfter']['valid_end_time']),
						'sale_account' => $data['DataPlanAfter']['sale_account'],
						'order_account' => $data['DataPlanAfter']['order_account'],
						'remark' => $remark,
						'create_time' => time(),
					);
					M('CustomHandleLog')->add($log);//日志记录
				}else{
					$order_mod->where(array('id'=>$rs))->save(array('deal_status'=>1,'deal_remark'=>'套餐不存在'));
				}
			}else{
				$order_mod->where(array('id'=>$rs))->save(array('deal_status'=>1,'deal_remark'=>'客户不存在'));
			}
			$this->ajaxReturn(array('code'=>200));exit;
		}
	}
	
 	public function deal_refund_order()
	{
		$message = I('post.');
		if(empty($message['data']))
		{
			$this->ajaxReturn(array('code'=>403));exit;
		}
		$data = json_decode(base64_decode($message['data']),true);
		$refund_mod = M('RefundOrder');
		if($refund_mod->where(array('order_sn'=>$data['OrderNo']))->count()>0)
		{//重复订单过滤
			$this->ajaxReturn(array('code'=>200));exit;
		}
		$custom_info = M('custom')->where(array('imsi'=>$data['IMSI']))->field('custom_no,company')->find();
		$refund_temp = array(
			'order_sn' => $data['OrderNo'],
			'company' => $custom_info['company'],
			'custom_no' => $custom_info['custom_no'],
			'package_sn' => $data['DataPlanBefore']['ID'],
			'imsi' => $data['IMSI'],
			'valid_start_time' => strtotime($data['DataPlanAfter']['valid_start_time']),
			'valid_end_time' => strtotime($data['DataPlanAfter']['valid_end_time']),
			'refund_account' => $data['Money']/100,
			'refund_time' => strtotime($data['OrderTime']),
			'create_time' => time(),
			'del' => 0,
		);
		$rs = $refund_mod->add($refund_temp);
		if($rs)
		{
			$map = array(
				'package_sn' => $refund_temp['package_sn'],
				'custom_no' => $refund_temp['custom_no'],
			);
			$handle_mod = M('CustomHandleLog');
			if($handle_mod->where($map)->where(array('valid_start_time' => $refund_temp['valid_start_time'],'valid_end_time' => $refund_temp['valid_end_time']))->save(array('order_status'=>2))!==false)
			{
				//追回续费时间
				$package_info = M('Package')->where(array('package_sn' => $refund_temp['package_sn']))->field('cycle_value')->find();
				$mod = M('UserPackage');
				$expense_account = $handle_mod->where($map)->where(array('order_status'=>array('neq',2)))->order('id desc')->getField('order_account');
				if($mod->where($map)->save(array('service_end_time'=>strtotime($data['DataPlanAfter']['EndTime']),'expense_price'=>$expense_account))!== false)
				{
					$this->ajaxReturn(array('code'=>200));exit;
				}
			}else{
				$this->ajaxReturn(array('code'=>403));exit;
			}
		}else{
			$this->ajaxReturn(array('code'=>403));exit;
		}
	}
}
