<?php
class Html_Table{
	public $data;
	public $th_array = array();
	public $td_array = array();
	public $col_width_array = array();
	public $table_class = '';
	public $td_class="class='op'";
	
	
	function __construct($data) {
		$this->data = $data;
		$this->defaultSet();
	}
	
	public function setTableInfo($key_array){
		$th_array = array();
		$td_array = array();
		foreach ($key_array as $index => $one){
			if(is_numeric($index)){
				$index = $one;
			}
			
			$th_array[$index] = $one;
			$td_array[$index] = $index;
		}
		
		$this->th_array = $th_array;
		$this->td_array = $td_array;
	}
	
	/**
	 * $rows_info = array(
	 * 	   'username' => array('title' => '','col_width' => '')
	 * 
	 * )
	 * @param unknown_type $rows_info
	 */
	public function setDetailInfo($rows_info){
		foreach ($rows_info as $index => $one){
			$title = $one['title'];
			$td_index = $index;
			$col_width = $one['col_width'];
			
			$th_array[$index] = $title;
			$td_array[$index] = $index;
			$col_array[$index] = $col_width;
		}	

		$this->th_array = $th_array;
		$this->td_array = $td_array;
		$this->col_width_array = $col_array;
	}
	
	public function createHtml(){
		$th_str = "";
		$row_str = "";
		$col_str = "";

		foreach($this->th_array as $index => $one){
			$th_str .="<th>{$one}</th>";
		}
		
		foreach ($this->col_width_array as $index => $one){
			$col_str .= "<col width='{$one}'>";
		}
		
		if(!is_array($this->data) || !$this->data){
			$col_span = count($this->th_array);
			$row_str = "<tr><td colspan='{$col_span}' class='op'>没有发现数据</td><tr>";
		} else {
			foreach ($this->data as $index => $one){
				$td_str = "";
				if($this->td_array){
					foreach ($this->td_array as $i => $o){
						$td_str .= "<td {$this->td_class}>{$one[$o]}</td>";
					}
				} else {
					foreach ($one as $value){
						$td_str .= "<td {$this->td_class}>{$value}</td>";
					}
				}

				$class = "";
				if($one['tr_class']){
					$class =" class='{$one['tr_class']}'";
				}
				$row_str .= "<tr {$class}>{$td_str}</tr>";
			}
		}
		$html = "
			<table class='{$this->table_class}' border='1'>
			{$col_str}
			<tr>
			{$th_str}
			</tr>
			{$row_str}
			</table>
		";
		return $html;
	}
	
	public function defaultSet(){
		$data = $this->data;
		$modelData = current($data);
		$modelData = $modelData ? $modelData : array();
		$keyArray = array();
		foreach ($modelData as $key => $value){
			$keyArray[$key] = $key;
		}
		$this->setTableInfo($keyArray);
	}
}