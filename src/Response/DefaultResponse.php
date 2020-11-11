<?php
namespace Gyugie\Response;

class DefaultResponse
{
	private $result;
	
	public function __construct($res)
	{
		$this->result = isset($res->data) ? $res->data : $res;
	}
	
	public function getResult()
	{
		return $this->result;
	}
}
