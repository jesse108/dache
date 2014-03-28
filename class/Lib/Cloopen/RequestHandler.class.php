<?php
class Lib_Cloopen_RequestHandler{
	
	public static function HandleRequest(){
		$rawData = file_get_contents("php://input");
		$rawData = trim($rawData," \t\n\r");
		$data = simplexml_load_string($rawData);
		$data = Util_Array::ObjectToArray($data);
		$action = $data['action'];
		
		switch($action){
			case "SellingCall":
				$result = self::SellingCall($data);
				break;
			default:
				
				break;
		}
		$result = $result ? $result : '';
		Log::Set($result,2);
		$result = self::BuildXMLResponse($result);
		echo $result;
	}
	

	
	public static function SellingCall($data){
		$callSid = $data['callSid'];
		$number = $data['number'];
		$state = $data['state'];
		$duration = $data['duration'];
		
		$result = array(
			'statuscode' => '000000',
		);
		return $result;
	}
	
	
	public static function BuildXMLResponse($result){
		if(!Util_Array::IsArrayValue($result)){
			return $result;
		}
		
		//header('Content-Type: text/xml; charset=utf8;');
		$xml = "";
		foreach ($result as $index => $one){
			$xml .= "<{$index}>{$one}</{$index}>";
		}
		
		$xml = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<Response>		
	{$xml}
</Response>";
		return  $xml;
	}
	
	
	
}