<?php 
/*
 * 菜单控制器
 */
 
namespace Admin\Controller;

class AuthRuleController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('AuthRule');
    }
	
    /* 列表(默认首页)
     **/
	public function index(){
		$map = array('del'=>0);
		if(I('get.pid'))
		{
			$map = array('pid'=>I('get.pid'));
		}else{
			$map = array('pid'=>0);
			$this->assign('pid',$pid);
		}
		//根据搜索条件对模型数据进行搜索
		$data = $this->Model->where($map)->order('sort asc')->getField('id,pid,title,name,icon,type,hide,status');
		$this->assign('list',$data);
		$this->display();
		//以ajax的数据结构返回数据
		//$this->ajaxReturn ( $data );
	}
	
    /* 添加
     **/
	public function add(){
		if(IS_POST){
        	//如果是post请求，进行数据添加操作
            //获取提交数据
			$post_data=I('post.');
 
			//使用模型对数据进行自动完成和自动验证
			$data=$this->Model->create($post_data);
			if($data){
				//如果自动验证成功,进行下一步操作
                //向模型添加数据
				$result = $this->Model->add($data);
                //判断添加的结果
				if($result){
                	//添加成功
                    //通过行为执行操作，（如果数据库没有定义此行为不进行任何操作）
					action_log('Add_AuthRule', 'AuthRule', $result);
                    //返回成功信息
					$this->success ( "操作成功！",U('AuthRule/index',array('pid'=>$post_data['jump_pid'])));
				}else{
                	//添加失败
                    //向模型获取失败信息
					$error = $this->Model->getError();
                    //如果存在失败信息则返回失败信息，不存在则返回操作失败
					$this->error($error ? $error : "操作失败！");
				}
			}else{
				//向模型获取失败信息
                $error = $this->Model->getError();
                 //如果存在失败信息则返回失败信息，不存在则返回操作失败
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	//如果是get请求，显示页面模版
			
			$function_c = A("Function");
			$group_list = $function_c->get_auth_rule('-1');
			$this->assign('group_list',$group_list);
        	$this->display();
		}
	}
	
    /* 编辑
     **/
	public function edit(){
		if(IS_POST){
        	//如果是post请求，进行数据添加操作
            //获取提交数据
			$post_data=I('post.');
			//使用模型对数据进行自动完成和自动验证
			$data=$this->Model->create($post_data);
			if($data){
				//如果自动验证成功,进行下一步操作
                //根据$post_data中的ID数据，对模型中相应的数据进行更改
				$result = $this->Model->where(array('id'=>$post_data['id'],'del'=>0))->save($data);
                //判断修改的结果
				if($result){
                	//修改成功
                    //通过行为执行操作，（如果数据库没有定义此行为不进行任何操作）
					action_log('Edit_AuthRule', 'AuthRule', $post_data['id']);
                    //返回成功信息
					$this->success ( "操作成功！",U('AuthRule/index',array('pid'=>$post_data['jump_pid'])));
				}else{
                	//修改失败
                    //向模型获取失败信息
					$error = $this->Model->getError();
                    //如果存在失败信息则返回失败信息，不存在则返回操作失败
					$this->error($error ? $error : "操作失败！");
				}
			}else{
				//向模型获取失败信息
                $error = $this->Model->getError();
                 //如果存在失败信息则返回失败信息，不存在则返回操作失败
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	//如果是get请求
            //获取get传递的参数
			$_info=I('get.');
            //根据get中的ID字段，查询默认模型中的对应数据，赋值给$_info变量
			$_info = $this->Model->where(array('id'=>$_info['id'],'del'=>0))->find();
            //将$_info变量赋值给模版
			$this->assign('info', $_info);
            //显示更改页面
        	$this->display();
		}
	}
	
    /* 删除
     **/
	public function del(){
    	//获取get参数传递的ID字段
		$id=I('get.id');
        //判断ID如果小于1，说明传递参数错误
        if($id<1){
        	$this->error('参数不能为空！');
        }
		$child = $this->Model->where(array('pid'=>$id,'del'=>0))->count();
		if($child>0)
		{
			$this->error('存在子菜单，请先删除子菜单');
		}
        //通过主键值删除模型中的对应数据
		$res=$this->Model->delete($id);
        //判断删除结果
		if(!$res){
        	//失败 
			//向模型获取失败信息
			$error = $this->Model->getError();
			//如果存在失败信息则返回失败信息，不存在则返回操作失败
			$this->error($error ? $error : "操作失败！");
		}else{
        	//成功
        	//通过行为执行操作，（如果数据库没有定义此行为不进行任何操作）
			action_log('Del_AuthRule', 'AuthRule', $id);
            //返回操作成功信息
			$this->success('删除成功！');
		}
	}
}