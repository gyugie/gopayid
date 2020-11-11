<?php
namespace Gyugie;

class ParseResponse
{
	private $response;
	
	public function __construct($res, $url)
	{
		$res_json = json_decode($res);
		
		// NOTE : ACTION => gojek/v2
		if (isset($res_json->status) && $res_json->status != 'OK') throw new ParseException($url . ' ' . $res_json->message);
		// NOTE : ACTION => default
		if (isset($res_json->success) && $res_json->success == false) throw new ParseException($url . ' ' . $res_json->errors[0]->code . ' => ' . $res_json->errors[0]->message);
		
		$parts = parse_url($url);
		
		if ($parts['path'] == '/wallet/qr-code') {
			$this->response = new \Gyugie\Response\WalletResponse($res_json);
		} else if ($parts['path'] == '/wallet/history') {
			$this->response = new \Gyugie\Response\DefaultResponse($res_json);
		} else {
			$this->response =  new \Gyugie\Response\DefaultResponse($res_json);
		}
	}
	
	public function getResponse()
	{
		return $this->response;
	}
}

class ParseException extends \Exception
{}
