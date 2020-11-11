<?php
namespace Gyugie\Response;

class BalanceResponse
{
	private $balance;
	
	public function __construct($res)
	{
		$this->balance = $res->data->balance;
	}
	
	public function getBalance()
	{
		return $this->balance;
	}
}
