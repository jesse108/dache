<?php
interface Log_Model{
	public function log($data,$type = 1);

	public function get($start,$end,$type = null);
}