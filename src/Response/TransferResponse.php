<?php
namespace Gyugie\Response;

class TransferResponse
{
	private $ref;
	
	public function __construct($res)
	{
		$this->ref = $res->data->transaction_ref;
	}
	
	public function getRef()
	{
		return $this->ref;
	}
}
