<?php
class Util_Date{
	
	public static function ChinessDay($dayNum){
		$dayNum = intval($dayNum);
		$dayStr = '';
		
		switch ($dayNum){
			case 1:
				$dayStr = '一';
				break;
			case 2:
				$dayStr = '二';
				break;
			case 3:
				$dayStr = '三';
				break;
			case 4:
				$dayStr = '四';
				break;
			case 5:
				$dayStr = '五';
				break;
			case 6:
				$dayStr = '六';
				break;
			case 7:
				$dayStr = '日';
				break;
			default:
				$dayStr = '未知';
				break;
		}
		return  $dayStr;
	}
	
}