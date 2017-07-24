<?php 
/*
 * 订单管理
 */
 
namespace Admin\Controller;

class RefundController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		$this->Model = M('RefundOrder');
    }
	
    /*订单列表
     **/
	public function index(){
		$map = array(
			'del' => 0,
			'deal_status'=>0
		);
		$get_data = I('get.');
		$_GET['search'] = empty($get_data['search']) ? 0 : $get_data['search'];
		$page_search = '';
		if(!empty($get_data['order_sn']))
		{
			$map['a.order_sn'] = array('like','%'.$get_data['order_sn'].'%');
			$_GET['search'] = 1;
			$page_search['order_sn'] = $get_data['order_sn'];
		}
		if(!empty($get_data['imsi']))
		{
			$map['b.imsi'] = array('like','%'.$get_data['imsi'].'%');
			$_GET['search'] = 1;
			$page_search['imsi'] = $get_data['imsi'];
		}
		if(!empty($get_data['pay_type']))
		{
			$map['b.pay_type'] = array('eq',$get_data['pay_type']);
			$_GET['search'] = 1;
			$page_search['pay_type'] = $get_data['pay_type'];
		}
		if(!empty($get_data['service_type']))
		{
			$map['b.service_type'] = array('eq',$get_data['service_type']);
			$_GET['search'] = 1;
			$page_search['service_type'] = $get_data['service_type'];
		}
		if(!empty($get_data['pay_status']))
		{
			$map['b.pay_status'] = array('eq',$get_data['pay_status']);
			$_GET['search'] = 1;
			$page_search['pay_status'] = $get_data['pay_status'];
		}
		if(!empty($get_data['start']))
		{
			$map["FROM_UNIXTIME(a.refund_time, '%Y-%m-%d')"] = array('egt',$get_data['start']);
			$_GET['search'] = 1;
			$page_search['start'] = $get_data['start'];
		}
		if(!empty($get_data['end']))
		{
			$map["FROM_UNIXTIME(a.refund_time, '%Y-%m-%d')"] = array('elt',$get_data['end']);
			$_GET['search'] = 1;
			$page_search['end'] = $get_data['end'];
		}
		$db_prefix = C('DB_PREFIX');
		$count      = $this->Model->where($map)->join('a INNER JOIN '.$db_prefix.'user_order b ON a.order_sn=b.order_sn')->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		if($page_search)
		{
			$Page->parameter = $page_search;//分页条件检索
			$page_con = http_build_query($page_search);//跳转分页条件检索
		}
		$show       = $Page->show();// 分页显示输出
		$order_list = $this->Model->where($map)->field('a.order_sn,a.package_sn,a.custom_no,a.refund_time,b.imsi,b.pay_type,b.service_type,b.pay_sn,b.pay_value,b.order_time,b.pay_status')->order('a.refund_time desc')->join('a INNER JOIN '.$db_prefix.'user_order b ON a.order_sn=b.order_sn')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$order_list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('page_search',$page_con);//跳转分页条件检索
		$this->assign('page_count',$Page->totalRows);//数据总条数
		$this->assign('page_num',$Page->totalPages);//总页数
		$this->display ();
	}
}