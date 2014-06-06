<?php
class Html_Page{
	public $pageNo;
	public $pageSize;
	public $total;
	public $showMaxPage = 4;
	
	public function __construct($pageSize,$total,$pageNo = null){
		$pageNo = $pageNo === null ? $_GET['page_no'] : $pageNo;
		$pageNo = intval($pageNo);
		$pageNo = $pageNo > 0 ? $pageNo : 1;
		
		$this->pageNo = $pageNo;
		$this->pageSize = intval($pageSize);
		$this->total = intval($total);
	}
	
	
	public function getOffset(){
		return ($this->pageNo - 1) * $this->pageSize;
	}
	
	public function getHtml(){
		$html = "";
		$showPage = $this->getShowPage();
		
		if(Util_Array::IsArrayValue($showPage)){
			$url = $this->getUrl();
			foreach ($showPage as $pageInfo){
				$currentUrl = $url . "&page_no={$pageInfo['page_no']}";
				$class= '';
				if($pageInfo['current']){
					$html .= "<button class='btn btn-success'>{$pageInfo['title']}</button>";
				} else { 
					$html .= "<a href='{$currentUrl}'><button class='btn btn-default'>{$pageInfo['title']}</button></a>";
				}
				
			}
		}
		
		$html = "<div style=''>{$html}</div>";
		return $html;
	}
	
	private function getShowPage(){
		$totalPage = ceil( doubleval($this->total) / $this->pageSize);
		$showPage = array();
		if($totalPage > $this->showMaxPage){
			$showPage[] = array("title" => '首页','page_no' => 1);
		}
		
		if($this->pageNo > 1){
			$showPage[] = array("title" => '上一页','page_no' => $this->pageNo - 1);
		}
		
		//计算显示开始页
		$tempPage = $this->showMaxPage / 2 ;
		$startPage = $this->pageNo - $tempPage;
		
		$startAdd = $tempPage - ($totalPage - $this->pageNo);
		if($startAdd > 0){
			$startPage = $startPage - $startAdd;
		}
		$startPage = $startPage > 0  ? $startPage : 1;
		
		for($i=0; $i < $this->showMaxPage; $i++){
			$currentPage = $startPage + $i;
			if($currentPage > $totalPage){
				break;
			}
			if($currentPage <= 0 ){
				continue;
			}
			
			$pageInfo = array("title" => $currentPage,'page_no' => $currentPage);
			if($currentPage == $this->pageNo){
				$pageInfo['current'] = 1;
			}
			$showPage[] = $pageInfo;
		}
		
		if($this->pageNo < $totalPage){
			$showPage[] = array("title" => '下一页','page_no' => $this->pageNo + 1);
		}
		
		if($totalPage > $this->showMaxPage){
			$showPage[] = array("title" => '末页','page_no' => $totalPage);
		}
		return $showPage;
	}
	
	private function getUrl(){
		$uri = Utility::getRequestURI();
		$uriInfo = parse_url($uri);
		parse_str($uriInfo['query'],$queryInfo);
		unset($queryInfo['page_no']);
		
		$url = $uriInfo['path'] . '?' . Util_HttpRequest::BuildHttpQuery($queryInfo);
		return $url; 
	}
}