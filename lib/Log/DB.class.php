<?php
class Log_DB implements Log_Model{
	public $dbType;
	public $table;
	
	public function __construct($dbType =null,$table = 'log'){
		if(!$dbType){
			$config = Config::Get('log');
			$config = $config['db_config'];
			$this->dbType = $config['db_type'];
			$this->table = $config['default_table'];
		} else {
			$this->dbType = $dbType;
			$this->table = $table;
		}
	}
	
	/**
	 * 记日志 实现
	 * @see Log_Model::log()
	 */
	public function log($data, $type = 1) {
		$data = self::buildData($data);
		
		$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$log = array(
			'type' => intval($type),
			'url' => $url,
			'post_data' => $GLOBALS["HTTP_RAW_POST_DATA"],
			'content' => $data,
			'create_time' => time(),
		);
		return DB::Insert($this->table, $log,null,$this->dbType);
		
	}

	/* (non-PHPdoc)
	 * @see Log_Model::get()
	 */
	public function get($start, $end, $type = null) {
		$condition = array(
			"create_time >= {$start}",
			"create_time <= {$end}",
		);
		if($type !== null){
			$condition['type'] = $type;
		}
		$result = DB::LimitQuery($this->table,$condition,null,$this->dbType);
		return $result;
	}
	
	public static function buildData($data){
		if(is_array($data)){
			$data = http_build_query($data);
		}
		return $data;
	}

}