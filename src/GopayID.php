<?php
namespace Gyugie;

use Gyugie\HTTP\Curl;

/**
 * [Gojek] GopayID Api PHP Class (Un-Official)
 * Author : mugypleci <https://github.com/gyugie>
 * Created at 22-04-2020 14:26
 * Last Modified at 11-11-2020
 */
class GopayID
{
    const ApiUrl = 'https://goid.gojekapi.com';
    const Api2Url = 'https://api.gojekapi.com';
    const appId = 'com.go-jek.ios';
    const phoneModel = 'Apple, iPhone11,6';
    const phoneMake = 'Apple';
    const osDevice = 'iOS, 13.3.1';
    const xPlatform = 'iOS';
    const appVersion = '3.51';
    const clientId = 'gojek:consumer:app';
    const clientSecret = 'pGwQ7oi8bKqqwvid09UrjqpkMEHklb';
    const userAgent = 'Gojek/3.51 (com.go-jek.ios; build:6890866; iOS 13.3.1) Alamofire/3.51';

    private $authToken;
    
    private $pin;
    
    private $sessionId;
    
    private $uniqueId;
    
    private $curl;
    
    public function __construct($token = false)
    {
        $this->curl         = new Curl();
        $this->sessionId    = '78EB815C-6AE5-4969-A6B1-BE5EC893F7AA'; // generated from self::uuidv4();
        $this->uniqueId     = '5C816FEA-D93E-4910-B672-978FCFA992F2'; // generated from self::uuidv4();
       
        if ($token) {
            $this->authToken = $token;
        }
    }
    
    public function uuidv4()
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }
    
     /**
	 * transferBank
	 * 
     * @param string $bankCode
     * @param string $bankNumber
     * @param float $amount
     * @param integer $pin
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function transferBank($bankCode, $bankNumber, $amount, $pin)
    {
        $bankAccountName = self::getBankAccountName($bankCode, $bankNumber);
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber,
            'amount' => $amount,
            'bank_account_name' => $bankAccountName
        );
        self::setPin($pin);

        return $this->curl->post(self::Api2Url . '/v3/wallet/withdrawal/request', $payload, self::buildHeaders())->getResponse();
    }  

    /**
	 * transferGopayID
	 * 
     * @param string $phoneNumber
     * @param float $amount
     * @param integer $pin
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function transferGopayID($phoneNumber, $amount, $pin)
    {
        self::setPin($pin);
        $payload = array(
            'qr_id' => self::getQrid($phoneNumber) ,
            'amount' => $amount,
            'description' => '💰'
        );

        return $this->curl->post(self::Api2Url . '/v2/fund/transfer', $payload, self::buildHeaders())->getResponse();
    }

    /**
	 * setPin
	 * 
     * @param integer $pin
	 * @return integer 
	 */
    protected function setPin($pin)
    {
        $this->pin = $pin;
    }

    /**
	 * getRealAmount
	 * 
     * @param float $amount
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getRealAmount($amount)
    {
        return $this->curl->get(self::Api2Url . '/wallet/withdrawal/request?amount=' . $amount, [], self::buildHeaders())->getResponse();
    }

     /**
	 * getBankList
	 * 
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getBankList()
    {
        return $this->curl->get(self::Api2Url . '/v1/withdrawal/banks', [], self::buildHeaders())->getResponse();
    }

     /**
	 * getHistory
	 * 
     * @param integer $page
     * @param integer $limit
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getHistoryTransaction($page = 1, $limit = 20)
    {
        return $this->curl->get(self::Api2Url . "/wallet/history?" . http_build_query([ 'page' => $page, 'limit' => $limit ]), [], self::buildHeaders())->getResponse();
    }

     /**
	 * getProfile
	 * 
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getProfile()
    {
        return $this->curl->get(self::Api2Url . '/gojek/v2/customer', [], self::buildHeaders())->getResponse();
    }

    /**
	 * getAuthToken
	 * 
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getBalance()
    {
        return $this->curl->get(self::Api2Url . '/wallet/profile', [], self::buildHeaders())->getResponse();
    }

    /**
	 * getQrid
	 * 
	 * @param String			$phoneNumber
	 * @return \Namedevel\Response\WalletResponse
	 */
    public function getQrid($phoneNumber)
    {
        return $this->curl->get(self::Api2Url . '/wallet/qr-code?phone_number=' . urlencode($phoneNumber), [], self::buildHeaders())->getResponse();
    }

    /**
	 * getAuthToken
	 * 
	 * @param String			$otpToken
     * @param String            $otpCode
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getAuthToken($otpToken, $otpCode)
    {
        $payload = array(
            'data' => array(
                'otp_token' => $otpToken,
                'otp' => $otpCode
            ) ,
            'client_id' => self::clientId,
            'grant_type' => 'otp',
            'client_secret' => self::clientSecret
        );

        return $this->curl->post(self::ApiUrl . '/goid/token', $payload, self::buildHeaders())->getResponse();
    }

    /**
	 * getBankAccountName
	 * 
	 * @param String			$bankCode
     * @param String            $bankNumber
	 * @return \Namedevel\Response\DefaultResponse
	 */
    public function getBankAccountName($bankCode, $bankNumber)
    {
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber
        );

        return $this->curl->post(self::Api2Url . '/v1/withdrawal/account/validate', $payload, self::buildHeaders())->getResponse();
    }

    /**
	 * LoginNumberPhone
	 * 
	 * @param String			$mobilePhone
	 * @return \Namedevel\Response\LoginPhoneResponse
	 */
	
    public function LoginNumberPhone($phoneNumber)
    {
        $payload = array(
            'client_id' => self::clientId,
            'client_secret' => self::clientSecret,
            'country_code' => '+62',
            'phone_number' => $phoneNumber
        );

        return $this->curl->post(self::ApiUrl . '/goid/login/request', $payload, self::buildHeaders())->getResponse();
    }

    /**
     * build headers request
     * 
     * @return array
     */
    protected function buildHeaders()
    {
        $headers = array(
            'x-appid: ' . self::appId,
            'x-phonemodel: ' . self::phoneModel,
            'user-agent: ' . self::userAgent,
            'x-session-id: ' . $this->sessionId,
            'x-phonemake: ' . self::phoneMake,
            'x-uniqueid: ' . $this->uniqueId,
            'x-deviceos: ' . self::osDevice,
            'x-platform: ' . self::xPlatform,
            'x-appversion: ' . self::appVersion,
            'accept: */*',
            'content-type: application/json',
            'x-user-type: customer'
        );

        if (!empty($this->authToken)) {
            array_push($headers, 'Authorization: Bearer ' . $this->authToken);
        }
        if (!empty($this->pin)) {
            array_push($headers, 'pin: ' . $this->pin);
        }

      
        return $headers;
    }

    /**
	 * Logout GOJEK
	 * 
	 * @return \Gyugie\Response\DefaultResponse
	 */
	
	public function logout()
	{
		return $this->curl->delete(self::Api2Url . '/v3/auth/token', [], self::buildHeaders())->getResponse();
	}

}
