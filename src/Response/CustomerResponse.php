<?php
namespace Gyugie\Response;

class CustomerResponse
{
	private $result;
	
	public function __construct($res)
	{
		$this->result = $res->customer;
	}
	
	public function getResult()
	{
		return $this->result;
	}
}
