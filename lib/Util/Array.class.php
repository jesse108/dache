<?php
class Util_Array{
	/**
	 * 获取一个二维数组的一列数据
	 * 
	 * @param array $array 输入数组
	 * @param string $colKey 指定列
	 * @return array 
	 */
	public static function GetColumn($array,$colKey){
		if(!$array || !is_array($array) || !$colKey){
			return null;
		}
		if(is_object($array)){
			$array = self::ObjectToArray($array);
		}
		
		$colArray = array();
		foreach ($array as $index => $value){
			if(is_object($value)){
				$curValue = $value->$colKey;
			} else if(is_array($value)){
				$curValue = $value[$colKey];
			} else {
				continue;
			}
			
			if(isset($curValue) && $curValue !== ''){
				$colArray[$curValue] = $curValue;
			}
		}
		$colArray = array_values($colArray);
		return $colArray;
	}
	
	/**
	 * 常用返回数组数据判断
	 * 判断返回的数组数据是否正确
	 * 
	 */
	public static function IsArrayValue($data){
		if(!$data || !is_array($data) || empty($data)){
			return false;
		}
		return true;
	}
	
	/**
	 * 将数组按指定列的值 为关键字 构造新数组返回
	 * 注意 如果有多个数据   关键字相同将会覆盖
	 * 
	 * @param array $array 输入数组
	 * @param string $colKey 指定列名
	 * @return array 构造好的数组
	 */
	public static function AssColumn($array,$colKey){
		if(!$array || !is_array($array) || !$colKey){
			return null;
		}
		
		$newArray = array();
		foreach ($array as $index => $one){
			$key = $one[$colKey];
			if(isset($key) && !isset($newArray[$key])){
				$newArray[$key] = $one;
			}
		}
		return $newArray;
	}
	
	/**
	 * 对象转化成数组
	 * @param obj $obj 对象
	 * @return array 转化后的数组
	 */
	public static function ObjectToArray($obj){
		$_arr = is_object($obj) ? get_object_vars($obj) :$obj;
		foreach ($_arr as $key=>$val){
			$val = (is_array($val) || is_object($val)) ? self::ObjectToArray($val):$val;
			$arr[$key] = $val;
		}
		return $arr;
	}
	
	/**
	 * 对数组排序
	 * @param unknown $array
	 * @param string $order
	 * @param string $key
	 * @return multitype:
	 */
	public static function Sort($array,$key = null,$order = SORT_ASC){
		if(!self::IsArrayValue($array)){
			return array();
		}
		$keyArray = array();
		$sortedArray = array();
		
		//分配
		foreach ($array as $index =>$value){
			$currentKey = '';
			if(is_array($value)){
				if(!$key){
					$currentKey = $index;
				} else if(isset($value[$key])){
					$currentKey = $value[$key];
				}
			} else {
				$currentKey = $value;
			}
			
			$keyArray[$currentKey][] = $index;
		}
		
		
		///排序
		switch ($order){
			case SORT_DESC:
				krsort($keyArray);
				break;
			case SORT_ASC:
			default:
				ksort($keyArray);
				break;
		}
		
		//组装
		foreach ($keyArray as $indexArray){
			foreach ($indexArray as $index){
				$sortedArray[$index] = $array[$index];
			}
		}
		
		return $sortedArray;
	
	}
	
	
	
	
	
	
	/////////////////////数组格式化
	/**
	 * 格式化嵌套数组
	 */
	private static $arrayFormatTrack = array();
	private static $arrayFormatTemp = array();
	
	public static function FormatInTree($array,$keyName = 'id',$parentKey = 'parent_id',$subName = 'sub'){
		self::$arrayFormatTrack = array();
		self::$arrayFormatTemp = $array;
		$tree = self::GetSubTree($array, 0,$keyName,$parentKey,$subName);
		self::$arrayFormatTrack = array();
		self::$arrayFormatTemp = array();
		return $tree;	
	}
	
	public static function GetSubTree($array = null,$parentID = null,$keyName = 'id',$parentKeyName='parent_id',$subName = 'sub',$assIndex = true){
		if(!$array){
			$data = self::$arrayFormatTemp;
		} else {
			$data = $array;
		}
		
		$tree = array();
		foreach ($data as $index => $one){
			$key = $one[$keyName];
			if(self::$arrayFormatTrack[$key] ||  //已经记录过
				($parentID && $one[$parentKeyName] != $parentID)//非子元素
				){
				continue;
			}
			self::$arrayFormatTrack[$key] = true;
			unset(self::$arrayFormatTemp[$index]);
			
			$subTree = self::GetSubTree($array,$key,$keyName,$parentKeyName,$subName,$assIndex);
			if($subTree){
				$one[$subName] = $subTree;
			}
			
			if($assIndex){
				$tree[$key] = $one;
			} else {
				$tree[] = $one;
			}
		}
		return $tree;
	}
	
	/**
	 * 在树形数组中寻找指定元素
	 * 
	 * @param array $tree   输入树形数组
	 * @param str $keyValue 寻找Key值
	 * @param string $keyName 键值名称 默认为 id
	 * @param string $subName 子树名称
	 * @return  找到的元素
	 */
	public static function FindNodeInTree($tree,$keyValue,$keyName = 'id',$subName = 'sub'){
		if(!Util_Array::IsArrayValue($tree)){
			return false;
		}
		
		foreach ($tree as $index => $one){
			if($one[$keyName] == $keyValue){
				return $one;
			}
			
			if($one[$subName]){
				$subResult = self::FindNodeInTree($one[$subName], $keyValue,$keyName,$subName);
				if($subResult){
					return $subResult;
				}
			}
		}
		
		return false;
	}
	/////////////////////////////////////////
}