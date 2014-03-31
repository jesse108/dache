<?php
class DB{
	const DB_TYPE_RW = 'rw';
	const DB_TYPE_RO = 'ro';
	
	private static $_dbObjectList = array();
	public static $error;
	private static $_debug =false;
	
	
	public static function Init($dbType = null){
		$dbType = $dbType ? $dbType : self::DB_TYPE_RW;
		if(!self::$_dbObjectList[$dbType]){
			$dbConfigList = Config::Get('db');
			$dbConfig = $dbConfigList[$dbType];
			if($dbConfig){
				$dbObject = new DBObject($dbConfig);
				$dbObject->debug = self::$_debug;
				self::$_dbObjectList[$dbType] = $dbObject;
			}
		}
	}
	
	public static function Query($sql,$dbType = null){
		$dbObject = self::_GetDBObject($dbType);
		$result = $dbObject->query($sql);
		if($result){
			return $result;
		} else {
			self::$error = $dbObject->error;
			return false;
		}
	}
	
	static public function Insert ($table, $condition, $duplicateCondition = null, $dbType = null) {
		$dbType = $dbType ? $dbType : self::DB_TYPE_RW;
		$sql = "INSERT INTO `$table` SET ";
		$content = self::BuildUpdateCondition($condition);
		
		$sql .= $content;
		
		if ($duplicateCondition) {
			$duplicateCondition = self::BuildUpdateCondition($duplicateCondition,$dbType);
			if($duplicateCondition){
				$sql .= " ON DUPLICATE KEY UPDATE {$duplicateCondition}";
			}
		}
		
		$result = self::Query ($sql,$dbType);
		if ( false == $result) {
			return false;
		}
		if ($duplicateCondition)
			return true;
		($insert_id = self::GetInsertID ()) || ($insert_id = true);
		return $insert_id;
	}
	
	
	public static function GetQueryResult($sql,$one = false,$dbType = null){
		$ret = array();
		$result = self::Query($sql,$dbType);
		if(!$result){
			return false;
		}
		
		while ($row=mysql_fetch_assoc($result)){
			if($one){
				return $row;
				break;
			}
			$ret[] = $row;
		}
		@mysql_free_result($result);
		return $ret;
		
	}
	
	public static function Count($table,$condition,$sum = null,$dbType = self::DB_TYPE_RO){
		$sum = $sum ? "sum({$sum}) count" : 'count(1) count';
		$option = array(
			'select' => $sum,
			'one' => true,
		);
		$result = self::LimitQuery($table,$condition,$option,$dbType);
		if($result){
			return $result['count'];
		} else {
			return 0;
		}
	}
	
	static public function LimitQuery ($table,$condition = array(),$options = array(),$dbType = self::DB_TYPE_RO) {
		$dbType = $dbType ? $dbType : self::DB_TYPE_RO;
		$condition = $condition ? $condition : null;
		$one = isset ( $options [ 'one'] ) ? $options [ 'one'] : false;
		$offset = isset ( $options [ 'offset'] ) ? abs ( intval ( $options ['offset'] ) ) : 0;
		if ($one) {
			$size = 1;
		} else {
			$size = isset ( $options [ 'size'] ) ? abs ( intval ( $options ['size'] ) ) : null ;
		}
		$select = isset ( $options [ 'select'] ) ? $options ['select'] : '*';
		$order = isset ( $options [ 'order'] ) ? $options ['order'] : null;
		$groupby = isset ( $options ['group'] ) ? $options ['group'] : null;
		 
	
		$condition = self:: BuildCondition( $condition );
		$condition = ( $condition == null) ? null : "WHERE $condition" ;
		 
		$limitation = $size ? "LIMIT $offset ,$size " : null;
		 
		$sql = "SELECT {$select} FROM `$table` $condition $groupby $order $limitation" ;
		return self:: GetQueryResult ( $sql, $one);
	}
	
	public static function Update($table,$condition,$updateRow,$dbType = null){
		$dbType = $dbType ? $dbType : self::DB_TYPE_RW;
		$condition = self::BuildCondition($condition,'AND',$dbType);
		$updateRow = self::BuildUpdateCondition($updateRow);
		
		$sql = "UPDATE `{$table}` SET {$updateRow} ";
		if($condition){
			$sql .= " WHERE {$condition}";
		}
		return self::Query($sql,$dbType);
	}
	
	public static function Delete($table,$condition,$dbType = null){
		$dbType = $dbType ? $dbType : self::DB_TYPE_RW;
		$condition = self::BuildCondition($condition,'AND',$dbType);
		
		$sql = "DELETE FROM `{$table}` ";
		if($condition){
			$sql .= " WHERE {$condition}";
		}
		return self::Query($sql,$dbType);
	}
	
	public static function Exists($table,$condition,$column = 'id',$dbType = null){
		$condition = self::BuildCondition($condition,'AND',$dbType);
		$sql = "SELECT {$column} from `{$table}` ";
		if($condition){
			$sql .= " WHERE {$condition}";
		}
		$sql .= ' limit 1 ';
		$result = self::GetQueryResult($sql,$dbType);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	static public function BuildCondition ($condition = array(), $logic = 'AND',$dbType = null) {
		$logic = $logic ? $logic : 'AND';
		if (is_string ( $condition ) || is_null ( $condition ))
			return $condition;
		 
		$logic = strtoupper ( $logic );
		$content = null;
		foreach ( $condition as $k => $v ) {
			$v_str = null;
			$v_connect = '=';
	
			if (is_numeric ( $k )) {
				$content .= $logic . ' (' . self:: BuildCondition ( $v, $logic,$dbType) . ')';
				continue;
			}
			$k = preg_replace ( '/[\#\;\=\s]+/', '', $k );
	
			$maybe_logic = strtoupper ( $k );
			if (in_array ( $maybe_logic, array ( 'AND', 'OR' ) )) {
				$content .= $logic . ' (' . self:: BuildCondition ( $v, $maybe_logic,$dbType) . ')';
				continue;
			}
	
			if (is_numeric ( $v )) {
				$v_str = "'{$v}'";
			} else if (is_null ( $v )) {
				$v_connect = ' IS ';
				$v_str = ' NULL';
			} else if (is_array ( $v )) {
				if ( isset ( $v [0] )) {
					$v_str = null;
					foreach ( $v as $one ) {
						if (is_numeric ( $one )) {
							$v_str .= ',\'' . $one . '\'';
						} else {
							$v_str .= ',\'' . self::EscapeString ( $one ,$dbType) . '\'';
						}
					}
					$v_str = '(' . trim ( $v_str, ',' ) . ')';
					$v_connect = 'IN';
				} else if ( empty ( $v )) {
					$v_str = $k;
					$v_connect = '<>';
				} else {
					$v_connect = array_shift ( array_keys ( $v ) );
					$v_connect = preg_replace ( '/[\#\;\=\s]+/', '', $v_connect );
					$v_s = array_shift ( array_values ( $v ) );
					$v_str = "'" . self::EscapeString ( $v_s ,$dbType) . "'";
					$v_str = is_numeric ( $v_s ) ? "'{$v_s} '" : $v_str;
				}
			} else {
				$v_str = "'" . self::EscapeString ( $v ,$dbType) . "'";
			}
	
			$content .= " $logic `$k` $v_connect $v_str ";
		}
		 
		$content = preg_replace ( '/^\s*' . $logic . '\s*/', '', $content );
		$content = preg_replace ( '/\s*' . $logic . '\s*$/', '', $content );
		$content = trim ( $content );
		 
		return $content;
	}
	
	public static function BuildUpdateCondition($condition = array(),$dbType = null){
		if(is_string($condition)){
			return $condition;
		}
		 
		if(!Util_Array::IsArrayValue($condition)){
			return '';
		}
		
		$conditionStr = '';
		foreach ($condition as $key => $value){
			if(is_numeric($value)){
				$value = "'{$value}'";
			} else if(is_null($value)){
				$value = 'NULL';
			} else if(is_array($value)){
				$value = implode(',', $value);
				$value = self::EscapeString($value,$dbType);
				$value = "'{$value}'";
			} else {
				$value = self::EscapeString($value,$dbType);
				$value = "'{$value}'";
			}
			$conditionStr .= "`{$key}` = {$value},";
		}
		$conditionStr = trim($conditionStr,',');
		return $conditionStr;
	}
	
	static public function GetInsertID($dbType = null) {
		$dbObject = self::_GetDBObject($dbType);
		return $dbObject->getInserID();
	}
	
	
	
	
	private static function _GetDBObject($dbType = null){
		$dbType = $dbType ? $dbType : self::DB_TYPE_RW;
		self::init($dbType);
		return self::$_dbObjectList[$dbType];
	}
	
	public static function Close($dbType = null){
		if($dbType){
			if(self::$_dbObjectList[$dbType]){
				self::$_dbObjectList[$dbType]->close();
				unset(self::$_dbObjectList[$dbType]);
			}
			return;
		} else {
			foreach (self::$_dbObjectList as $index => $dbObject){
				$dbObject->close();
				unset(self::$_dbObjectList[$index]);
			}
			self::$_dbObjectList = array();
		}
	}
	
	public static function EscapeString($string,$dbType = null){
		$dbObject = self::_GetDBObject($dbType);
		return $dbObject->escapeString($string);
	}
	
	public static function Debug($debug = null){
		if($debug === null){
			$debug = self::$_debug ? false : true;
		}
		$debug = $debug ? true : false;
		foreach (self::$_dbObjectList as $dbObject){
			$dbObject->debug = $debug;
		}
		self::$_debug = $debug;
	}
	
}





class DBObject{
	private $_connection = null;
	
	public $debug = false;
	public $error = null;
	public $count = 0;
	
	
	function __construct($dbConfig){
		if(!$dbConfig || !is_array($dbConfig)){
			throw new Exception("No DB Config");
			return;
		}
		$host = $dbConfig['host'];
		$password = $dbConfig['password'];
		$user = $dbConfig['user'];
		$name = $dbConfig['name'];
		
		$this->_connection = mysql_connect($host,$user,$password);
		
		if(mysql_errno()){
			throw new Exception("Connect failed: ".mysql_error());
		}
		@mysql_select_db($name,$this->_connection);
		@mysql_query('SET NAMES UTF8;',$this->_connection);
	}
	
	
	function query($sql){
		$this->count++;
		if($this->debug){
			Util_Time::timerStart($sql);
		}
		$result = @mysql_query($sql,$this->_connection);
		if($this->debug){
			$duration = Util_Time::timerStop($sql);
			$rowNum = mysql_affected_rows($this->_connection);
			$count = $this->count;
			echo "
			<pre>[{$count}][ROW:{$rowNum}][time:{$duration}]{$sql} </pre>
			";
		}
		if($result){
			return  $result;
		} else {
			$this->error = mysql_error($this->_connection);
			return false;
		}
		
	}
	
	function getInserID(){
		return intval ( @mysql_insert_id ($this->_connection) );
	}
	
	function escapeString($string){
		return @mysql_real_escape_string ( $string,$this->_connection);
	}
	
	
	
	
	
	////////////////////close
	public function close(){
		if(is_resource($this->_connection)){
			@mysql_close($this->_connection);
		}
		$this->_connection = null;
	}
	
	public function __destruct(){
		$this->close();
	}
	
	
}