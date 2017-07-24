<?php 
/*
 * 配置管理模型
 */
 
namespace Admin\Model;
use Think\Model;

class ConfigModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'config'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array( 
	);

	public function cache(){
		S('DB_CONFIG_DATA',null);
		$config = $this->where(array('status'=>1))->getField ( 'name,value' );
		S('DB_CONFIG_DATA', $config);
	}
}