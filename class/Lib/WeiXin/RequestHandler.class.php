<?php
class Lib_WeiXin_RequestHandler implements WeiXin_Handler{
	/* (non-PHPdoc)
	 * @see WeiXin_Handler::handleRquest()
	 */
	public function handleRquest($type, $content) {
		if(!$type || !$content){
			return false;
		}
		
		
	}


	public function handleText(){
		
	}
	
	public function handleEvent(){
		
	}
	
	
}