<?php
namespace Gyugie\Response;

class LoginAuthResponse
{
	private $authToken;
	
	public function __construct($res)
	{
		$this->authToken = $res->data->access_token;
	}
	
	public function getAuthToken()
	{
		return $this->authToken;
	}
}
