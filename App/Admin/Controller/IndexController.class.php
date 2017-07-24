<?php

namespace Admin\Controller;

/**
 * 后台首页控制器
 */
class IndexController extends AdminCoreController {
	
	public function test()
	{
		/* 		//构造消息发布者
        import('Common.Libs.AliyunhttpProducer');
        $producer = new \HttpProducer();
		//启动消息发布者
		$producer->process(); */
		print_r(date('h:i:s'));
		//构造消息订阅者
		import('Common.Libs.AliyunhttpConsumer');
		$consumer = new \HttpConsumer();
		//启动消息订阅者 
		$info = $consumer->process();
/* 		import('Common.Libs.AliyunhttpProducer');
        $producer = new \HttpProducer();
		//启动消息发布者
		$content = 'aaaaa';
		$info = $producer->process($content); */
		print_r($info);
		
		print_r(date('h:i:s'));exit;
	}

	public function deal_order()
	{
		$data = '{"Version":"1.0","Tag":"CKB","OrderNo":"S14657970266521","OrderTime":"2016-07-21 02;07:44","PayType":"1","Money":"60","PayStaus":0,"PayNo":"2016061321001004410233325242","SIM":"1064837768726","IMSI":"460040347700126","ICCID":"898602B3131690082003","DataPlanBefore":{"ID":"LT_100M_2MONTH","Flow":10.0,"Name":"10M套餐","ServiceCycle":12,"BeginTime":"2016-07-21T14:42:44.9352686+08:00","EndTime":"2017-07-21T14:42:44.9352686+08:00"},"DataPlanAfter":{"ID":"LT_100M_2MONTH","Flow":20.0,"Name":"20M套餐","ServiceCycle":12,"BeginTime":"2018-07-21T14:42:44.9364044+08:00","EndTime":"2019-07-21T14:42:44.9364044+08:00"}}
';
		$data = json_decode($data,true);
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
		if($data['DataPlanBefore'] && $data['DataPlanAfter'])
		{
			if($data['DataPlanBefore']['ID'] == $data['DataPlanAfter']['ID'])
			{
				$service_type = '10';
			}else{
				$service_type = '12';
			}
		}else{
			$service_type =  '10';
		}
		$order_temp = array(
			'order_sn' => $data['OrderNo'],
			'package_id' => $data['DataPlanBefore']['ID'],
			'new_package_id' => $data['DataPlanAfter']['ID'],
			'imsi' => $data['IMSI'],
			'pay_type' => $pay_type,
			'service_type' => $service_type,
			'pay_sn' => $data['PayNo'],
			'pay_account' => '',
			'pay_value' => $data['Money'],
			'order_time' => strtotime($data['OrderTime']),
			'pay_status' => $pay_status,
			'create_time' => time(),
			'del' => 0,
		);
		$rs = M('UserOrder')->add($order_temp);
		if($rs)
		{
			$package_mod = M('UserPackage');
			$custom_info = M('custom')->where(array('imsi'=>$data['IMSI']))->field('custom_no')->find();
			$package_info = $package_mod->where(array('imsi'=>$data['IMSI'],'custom_no'=>$custom_info['custom_no']))->find();
			if($package_info)
			{
				$extra = $remark = '';
				if($service_type == 10)
				{
					$end_time = strtotime(date('Y-m',strtotime('+'.$data['DataPlanAfter']['ServiceCycle'].' month ',$package_info['service_end_time'])));
					print_r($package_info['service_end_time']);echo '<br />';
					$package_mod->where(array('imsi'=>$data['IMSI'],'custom_no'=>$custom_info['custom_no']))->save(array('service_end_time'=>$end_time));//套餐续费
					$extra = '';
					$remark = '套餐续费';
				}
				$log = array(
					'type' => $service_type,
					'custom_no' => $custom_info['custom_no'],
					'handle_type' => 10,
					'content' => $data['DataPlanBefore']['ID'],
					'extra' => $extra,
					'remark' => $remark,
					'create_time' => time(),
				);
				M('CustomHandleLog')->add($log);//日志记录
			}else{
				M('UserOrder')->delete($rs);
			}
		}
		echo 'ok';exit;
	}
	
	
	public function push_order()
	{
		$data = array(
			'Version' => '1.0',
			'Tag' => $data['DataPlanBefore']['ID'],
			'new_package_id' => $data['DataPlanAfter']['ID'],
			'imsi' => $data['IMSI'],
			'pay_type' => $pay_type,
			'service_type' => $service_type,
			'pay_sn' => $data['PayNo'],
			'pay_account' => '',
			'pay_value' => $data['Money'],
			'order_time' => strtotime($data['OrderTime']),
			'pay_status' => $pay_status,
			'create_time' => time(),
			'del' => 0,
		);
	}
    /**
     * 后台首页操作
     */
    public function index(){
		header("Content-type:text/html;charset=utf-8");
		$menu = $this->get_menu();
		$this->assign ( 'AdminMenu', array_values($menu));
		$this->display ();
    }
	
    /**
     * 控制台
     */
    public function main(){
		$this->display ();
    }

	
    /**
     * 更新缓存
     */
    public function cache() {
        if (isset($_GET['type'])) {
			//设置Dir类
			$Dir = new \Common\Libs\Dir();
			//设置缓存模型
            $cache = D('Common/Cache');
			//获取当前更新类型
            $type = I('get.type');
			//设置执行超时
            set_time_limit(0);
			//根据更新类型选择执行代码
            switch ($type) {
				//更新站点数据缓存
                case "site":
                    //开始刷新缓存
                    $stop = I('get.stop', 0, 'intval');
					//如果没有stop，则证明正在执行系统定义缓存的更新
                    if (empty($stop)) {
						//防止异常终止代码执行
                        try {
                            //已经清除过的目录
                            $dirList = explode(',', I('get.dir', ''));
                            //删除缓存目录下的文件
							//RUNTIME_PATH 应用运行时目录（默认为 APP_PATH.'Runtime/'）
                            $Dir->del(RUNTIME_PATH);
                            //获取子目录
                            $subdir = glob(RUNTIME_PATH . '*', GLOB_ONLYDIR | GLOB_NOSORT);
							//如果还有目录存在继续执行
                            if (is_array($subdir)) {
                                foreach ($subdir as $path) {
                                    $dirName = str_replace(RUNTIME_PATH, '', $path);
                                    //忽略目录
                                    if (in_array($dirName, array('Cache', 'Logs'))) {
                                        continue;
                                    }
                                    if (in_array($dirName, $dirList)) {
                                        continue;
                                    }
                                    $dirList[] = $dirName;
                                    //删除目录
                                    $Dir->delDir($path);
                                    //防止超时，清理一个从新跳转一次
                                    $this->assign("waitSecond", 1);
                                    $this->success("清理缓存目录[{$dirName}]成功！", U('Index/cache', array('type' => 'site', 'dir' => implode(',', $dirList))));
                                    exit;
                                }
                            }
                            //更新开启其他方式的缓存
                            \Think\Cache::getInstance()->clear();
                        } catch (Exception $exc) {
                            
                        }
                    }
					//执行数据库中的缓存
                    if ($stop) {
                        $modules = $cache->getCacheList();
                        //需要更新的缓存信息
                        $cacheInfo = $modules[$stop - 1];
                        if ($cacheInfo) {
                            if ($cache->runUpdate($cacheInfo) !== false) {
                                $this->assign("waitSecond", 1);
                                $this->success('更新缓存：' . $cacheInfo['name'], U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)));
                                exit;
                            } else {
                                $this->error('缓存[' . $cacheInfo['name'] . ']更新失败！', U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)));
                            }
                        } else {
                            $this->assign("waitSecond", 3);
                            $this->success('缓存更新完毕,请刷新网站！', U('Index/cache'));
                            exit;
                        }
                    }
                    $this->success("即将更新站点缓存！", U('Index/cache', array('type' => 'site', 'stop' => 1)));
                    break;
				//更新站点模板缓存
                case "template":
                    //删除缓存目录下的文件
                    $Dir->del(RUNTIME_PATH);
                    $Dir->delDir(RUNTIME_PATH . "Cache/");
                    $Dir->delDir(RUNTIME_PATH . "Temp/");
                    //更新开启其他方式的缓存
                    \Think\Cache::getInstance()->clear();
                    $this->assign("waitSecond", 3);
                    $this->success("模板缓存清理成功！请刷新网站！", U('Index/cache'));
                    break;
				//清除网站运行日志
                case "logs":
                    $Dir->delDir(RUNTIME_PATH . "Logs/");
                    $this->assign("waitSecond", 3);
                    $this->success("站点日志清理成功！请刷新网站！", U('Index/cache'));
                    break;
				//为选择更新类型
                default:
                    $this->error("请选择清楚缓存类型！");
                    break;
            }
        } else {
            $this->display();
        }
    }
}