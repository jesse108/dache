<?php
class File{
	public $filePath;
	public $buff;
	private $handle;
	public $mode = 'r';
	
	
	public function __construct($filePath,$mode = 'r'){
		$this->filePath = $filePath;
		$this->mode = $mode;
	}
	
	public function exists(){
		return file_exists($this->filePath);
	}
	
	public function open(){
		$mode = $this->mode;
		$this->handle = fopen($this->filePath, $mode);
	}
	
	public function readALL(){
		if(!$this->handle){
			$this->open();
		}
		$handle = $this->handle;
		while ($line = fgets($handle)){
			$this->buff[] = $line;
		}
		return $this->buff;
	}
	
	
	public function close(){
		if(is_resource($this->handle)){
			fclose($this->handle);
		}
		
	}
	
}