<?php 
/*
 * 用户模型
 * Auth   : Ghj
 * Time   : 1444386899 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace User\Model;
use Think\Model;

class UserModel extends Model{

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'),
        array('nickname', 'require', '昵称不能为空！'),
        array('password', 'require', '密码不能为空！', 0, '', 1),
        array('username', '', '帐号名称已经存在！', 0, 'unique', 1),
        array('phone', 'require', '手机不能为空！'),
        array('phone', '/^((13[0-9])|(15[^4,\\d])|(18[0,5-9]))[0-9]{8}$/', '请输入正确的手机号码！',0,'regex',1),
        //array('phone', '', '手机已存在，请检查或者找回密码！', 0, 'unique', 1),
        array('email', 'require', '邮箱不能为空！'),
        //array('email', '', '邮箱已存在，请检查或者找回密码！', 0, 'unique', 1),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );

}