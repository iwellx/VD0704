<?php 
/*
 * 企业管理模型
 */
 
namespace Admin\Model;
use Think\Model;

class CustomModel extends Model{

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
		array('name', 'number', '客户名格式错误！',2),
		array('name','11','客户名长度错误！',2,'length'), // 验证长度
        array('card_from', 'require', '运营商编号不能为空！'),
        array('card_number', 'require', '卡号不能为空！'),
		array('card_number','1,13','卡号长度错误！',1,'length'), // 验证长度
        array('imsi', 'require', 'imsi不能为空！'),
		array('imsi', 'number', 'imsi格式错误！'),
		array('imsi','1,15','imsi长度错误！',1,'length'), // 验证长度
        array('iccid', 'require', 'iccid不能为空！'),
		array('iccid','1,20','iccid',1,'length'), // 验证长度
        array('bank_number', 'require', '银行卡号不能为空！'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );

	public function delete($id=0)
	{
		$mod = M('Custom');
		$pk = $mod->getPk();
		$result = $mod->where(array($pk=>$id,'del'=>0))->save(array('del'=>1));
		return $result;
	}
	
	public function check_fields($value,$type,$id=0)
	{
		$mod = M('Custom');
		$map = '';
		$map['del'] = 0;
		if($type == 'card_number')
		{
			$map['card_number'] = $value;
		}elseif($type == 'imsi')
		{
			$map['imsi'] = $value;
		}elseif($type == 'iccid')
		{
			$map['iccid'] = $value;
		}
		if($id>0)
		{
			$map['id'] = array('NEQ',$id);
		}
		if($mod->where($map)->count()>0)
		{
			return false;
		}else{
			return true;
		}
	}
	
	public function get_company($where,$field='')
	{
		$db_pre = C('DB_PREFIX');
		$company_info = $this->table($db_pre.'custom a')->join('inner join '.$db_pre.'company b ON a.company=b.id')->where($where)->field($field)->find();
		return $company_info;
	}
}