<?php

use PHPUnit\Framework\TestCase;
use Gyugie\Response\BalanceResponse;
use Gyugie\Response\LoginAuthResponse;
use Gyugie\Response\LoginPhoneResponse;
use Gyugie\GopayId;

class GopayIDTest extends TestCase
{
	public function testSetDeviceId()
	{
		$gopay = new GopayId();

		$session_id = $gopay->uuidv4();
		$unique_id = $gopay->uuidv4();

		$this->assertEquals([
			'session_id' => $session_id,
			'unique_id' => $unique_id
		], [
			'session_id' => (new GopayId($session_id, $unique_id))->getSessionId(),
			'unique_id' => (new GopayId($session_id, $unique_id))->getUniqueId()
		]);
	}

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

	public function testPhoneNumber()
	{
		$gopay = new GopayId();

		$phone_number = $gopay->formatPhone('089111111111');
		
		$this->assertTrue($phone_number === '89111111111');
	}

	public function testGopaySchenario()
	{
		/** generate device id 
			$session_id = (new GopayId(null, null, null))->uuidv4();
			$unique_id = (new GopayId(null, null, null))->uuidv4();
			print_r([
				$session_id,
				$unique_id
			]);exit;
		*/

		$credentials = [
			'phone_number' => '08XXXXXXX',
			'otp_token' => '3989ebf2-9cd3-4974-bbbd-1c1619d9f451',
			'otp_code' => '4622',
			'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IiJ9.eyJhdWQiOlsiZ29qZWs6Y29uc3VtZXI6YXBwIl0sImRhdCI6eyJhY3RpdmUiOiJ0cnVlIiwiYmxhY2tsaXN0ZWQiOiJmYWxzZSIsImNvdW50cnlfY29kZSI6Iis2MiIsImNyZWF0ZWRfYXQiOiIyMDE2LTExLTE4VDEwOjQ2OjMxLjM4MFoiLCJlbWFpbCI6Im11Z3lwbGVjaUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6InRydWUiLCJnb3BheV9hY2NvdW50X2lkIjoiMDEtMGRmZWIxNjQ2MmUxNGU2MjgzY2Y3NzI1OGVkYWNhMmMtMjkiLCJuYW1lIjoibXVnaSIsIm51bWJlciI6Ijg5NjY2NTI4MDc0IiwicGhvbmUiOiIrNjI4OTY2NjUyODA3NCIsInNpZ25lZF91cF9jb3VudHJ5IjoiSUQiLCJ3YWxsZXRfaWQiOiIxNjMyMzA2NDYxODEzODE3MzEifSwiZXhwIjoxNjM0MzkxNTE5LCJpYXQiOjE2MzE2OTcyMjQsImlzcyI6ImdvaWQiLCJqdGkiOiI4ODEwMDUwZC02NmQ0LTQ3M2QtYjBjNi1jODhlZjRiZjliNWYiLCJzY29wZXMiOltdLCJzaWQiOiI1NDRmODk1MC01MWVjLTQ0OTItOTg1Zi1lMGRjOWFkODIwZGUiLCJzdWIiOiIwODVlMDJkMC00MWQ2LTRjNTgtYmMxNi05YTVmMTgxNzFmNmYiLCJ1aWQiOiI1NTcxMTM1MjQiLCJ1dHlwZSI6ImN1c3RvbWVyIn0.UonDVGWa_lvRUOVQ3ayQkhljMThHgCaA4uzBL06J4MiOX0w02Zqt5PamMmBkcopRqJhhZGDGYTwmhNJX7GL4KWBh-ToTDma66z0BTirGibyEM8JxD7SlL5zfCRfB7aahG7BB87E3Z9VYMhTEJ7AZMtjhp8DuQRKIcJnjjTvhsIg',
			'session_id' => '8E65C778-2A2E-46C2-B862-741BF1B3763A',
			'unique_id' => 'B6BBA138-B431-4525-BE43-C7EB5FD5FA9F'
		];
		
		// $gopay = new GopayId($credentials['session_id'], $credentials['unique_id'], $credentials['access_token']);

		/** step 1 request OTP Code */
		// $login = $gopay->loginNumberPhone($credentials['phone_number']);
		// print_r($login);exit;

		/** step 2 request auth token */
		// $login = $gopay->getAuthToken($credentials['otp_token'], $credentials['otp_code']);
		// print_r($login);exit;

		/** finaly request do anything */
		// $history_transaction = $gopay->getHistoryTransaction();
		// $balance = $gopay->getBalance();
		// print_r([
		// 	'history' => $history_transaction,
		// 	'balance' => $balance
		// ]);exit;
	}
}