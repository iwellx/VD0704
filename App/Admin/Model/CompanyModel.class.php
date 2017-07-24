<?php 
/*
 * 企业管理模型
 */
 
namespace Admin\Model;
use Think\Model;

class CompanyModel extends Model{

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '企业名不能为空！'),
        array('unicom_name', 'require', '企业全称不能为空！'),
        array('email', 'email', '邮箱地址有误！',2),
        array('bank_number', 'require', '银行卡号不能为空！',2),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );

	public function delete($id=0)
	{
		$mod = M('Company');
		$pk = $mod->getPk();
		$result = $mod->where(array($pk=>$id,'del'=>0))->save(array('del'=>1));
		return $result;
	}
}