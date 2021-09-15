<?php

use PHPUnit\Framework\TestCase;
use Gyugie\Response\BalanceResponse;
use Gyugie\Response\LoginAuthResponse;
use Gyugie\Response\LoginPhoneResponse;
use Gyugie\GopayId;

class GopayIDTest extends TestCase
{
    public function testLoginPhoneResponse()
	{
		$data = <<<JSON
		{
			"data": { "login_token": "e16e7cf0-7621-419d-9f67-36aa8b919f34" }
		}
JSON;

        $loginToken = (new LoginPhoneResponse(json_decode($data)))->getLoginToken();
		$this->assertEquals("e16e7cf0-7621-419d-9f67-36aa8b919f34", $loginToken);
    }
    
    public function testLoginAuthResponse()
	{
		$data = <<<JSON
		{
			"data": { "access_token": "d5579ff6-d194-473a-b3cf-0b903f5f7324" }
		}
JSON;

        $authToken = (new LoginAuthResponse(json_decode($data)))->getAuthToken();
		$this->assertEquals("d5579ff6-d194-473a-b3cf-0b903f5f7324", $authToken);
	}
    

    public function testBalanceResponse()
	{
		$data = <<<JSON
		{
			"data": { "balance": "2500" }
		}
JSON;

        $balance = (new BalanceResponse(json_decode($data)))->getBalance();
		$this->assertEquals("2500", $balance);
    }

}