<?php 
/*
 * 用户组模型
 */
 
namespace Admin\Model;
use Think\Model;

class AuthGroupModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = ''; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);
	public function delete($id=0)
	{
		$mod = M('AuthGroup');
		$pk = $mod->getPk();
		$result = $mod->where(array($pk=>$id))->save(array('del'=>1));
		return $result;
	}
}