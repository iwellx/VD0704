<?php 
/*
 * 用户管理模型
 */
 
namespace Admin\Model;
use Think\Model;


class UserModel extends Model{

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'),
        array('nickname', 'require', '真实姓名不能为空！'),
        array('password', 'require', '密码不能为空！', 0, 'regex', 1),
        array('email', 'email', '邮箱地址有误！'),
        array('username', '', '帐号名称已经存在！', 0, 'unique', 1),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('password', 'md5', 3, 'function'),
    );

	public function delete($id=0)
	{
		$mod = M('User');
		$pk = $mod->getPk();
		$result = $mod->where(array($pk=>$id,'del'=>0))->save(array('del'=>1));
		return $result;
	}

    //获取登录用户的ip
    public function get_ip(){
        static $real_ip;
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $real_ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $real_ip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $real_ip=$_SERVER['REMOTE_ADDR'];
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $real_ip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $real_ip=getenv('HTTP_CLIENT_IP');
            }else{
                $real_ip=getenv('REMOTE_ADDR');
            }
        }
        return $real_ip;
    }
}