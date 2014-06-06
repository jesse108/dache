<?php
/**
 * 展示列表
 * @author zhaojian01
 *
 */
class Html_List{
	public $data;
	public $titleArray;
	public $class = "dl-horizontal";
	
	public function __construct($data){
		$this->data = $data;
	}
	
	
	public function setTitle($titleArray){
		$this->titleArray = $titleArray;
	}
	
	public function createHtml(){
		$titleArray = $this->titleArray;
		$titleArray = $titleArray ? $titleArray : $this->getDefaultTitleArray();
		$data = $this->data;
		$class= $this->class;
		
		$list = "";
		foreach ($titleArray as $index => $title){
			$value= $data[$index];
			$list .= "<dt>{$title}<dt> <dd>{$value}</dd>";
		}
		$html = "<dl class={$class}>{$list}</dl>";
		return $html;
	}
	
	private function getDefaultTitleArray(){
		$titleArray = array();
		foreach ($this->data as $index => $one){
			$titleArray[$index] = $index;
		}
		return $titleArray;
	}
}