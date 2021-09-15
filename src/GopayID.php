<?php
namespace Gyugie;

use Gyugie\HTTP\Curl;
use Gyugie\Response\DefaultResponse;

/**
 * [Gojek] GopayID Api PHP Class (Un-Official)
 * Author : mugypleci <https://github.com/gyugie>
 * Created at 22-04-2020 14:26
 * Last Modified at 11-11-2020
 */
class GopayID
{
    const API_URL = 'https://api.gojekapi.com';
    const API_GOID = 'https://goid.gojekapi.com';
    const API_CUSTOMER = 'https://customer.gopayapi.com';
    const clientId = 'gojek:consumer:app';
    const clientSecret = 'pGwQ7oi8bKqqwvid09UrjqpkMEHklb';
    const appId = 'com.go-jek.ios';
    const phoneModel = 'Apple, iPhone XS Max';
    const phoneMake = 'Apple';
    const osDevice = 'iOS, 14.4.2';
    const xPlatform = 'iOS';
    const appVersion = '4.20.1';
    const gojekCountryCode = 'ID';
    const userAgent = 'Gojek/4.20.1 (com.go-jek.ios; build:15832942; iOS 14.4.2) Alamofire/4.20.1';

    private string $authToken;
    
    private int $pin;

    private string $sessionId;

    private string $uniqueId;
    
    private $curl;
    
    public function __construct($session_id = null, $unique_id = null, $token = null)
    {
        $this->curl         = new Curl();
        
        if($session_id) {
            $this->sessionId = $session_id;
        }

        if($unique_id) {
            $this->uniqueId = $unique_id;
        }

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

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function formatPhone($phoneNumber, $areacode = '')
    {
        $local_country_code = substr_replace($phoneNumber, $areacode, 2);

        if ((int) $local_country_code === 62) {
            return substr_replace($phoneNumber, $areacode, 0, 2);
        };

        return substr_replace($phoneNumber, $areacode, 0, 1);
    }

    /**
     * transferBank
     *
     * @param string $bankCode
     * @param string $bankNumber
     * @param float $amount
     * @param int $pin
     * @return Response\DefaultResponse|Response\WalletResponse
     */
    public function transferBank(string $bankCode, string $bankNumber, float $amount, int $pin)
    {
        $bankAccountName = self::getBankAccountName($bankCode, $bankNumber);
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber,
            'amount' => $amount,
            'bank_account_name' => $bankAccountName
        );
        self::setPin($pin);

        return $this->curl->post(self::API_URL . '/v3/wallet/withdrawal/request', $payload, self::buildHeaders())->getResponse();
    }

    /**
     * transferGopayID
     *
     * @param string $phoneNumber
     * @param float $amount
     * @param int $pin
     * @return Response\DefaultResponse|Response\WalletResponse
     */
    public function transferGopayID(string $phoneNumber, float $amount, int $pin)
    {
        self::setPin($pin);
        $payload = array(
            'amount' => array(
                'currency' => 'IDR',
                'value' => $amount
            ),
            'description' => '💰',
            'metadata' => array(
                'post_visibility' => 'NO_SOCIAL',
                'theme_id' => 'THEME_CLASSIC'
            ),
            'payee' => array(
                'id' => self::getQrid($phoneNumber),
                'id_type' => 'GOPAY_QR_ID'
            )
        );
        return $this->curl->post(self::API_CUSTOMER . '/v1/funds/transfer', $payload, self::buildHeaders())->getResponse();
    }

    /**
     * setPin
     *
     * @param int $pin
     * @return void
     */
    protected function setPin(int $pin)
    {
        $this->pin = $pin;
    }

    /**
     * getRealAmount
     *
     * @param float $amount
     * @return Response\DefaultResponse|Response\WalletResponse
     */
    public function getRealAmount(float $amount)
    {
        return $this->curl->get(self::API_URL . '/wallet/withdrawal/request?amount=' . $amount, [], self::buildHeaders())->getResponse();
    }

     /**
	 * getBankList
	 * 
	 */
    public function getBankList()
    {
        return $this->curl->get(self::API_CUSTOMER . "/v1/banks?type=transfer&show_withdrawal_block_status=false", [], self::buildHeaders())->getResponse();
    }

    /**
     * getHistory
     *
     * @param int $page
     * @param int $limit
     * @return Response\DefaultResponse|Response\WalletResponse
     */
    public function getHistoryTransaction($page = 1, $limit = 20)
    {
        return $this->curl->get(self::API_CUSTOMER . "/v1/users/transaction-history?page={$page}&limit={$limit}", [], self::buildHeaders())->getResponse();
    }

    /**
     * getProfile
     *
     * @return Response\DefaultResponse|Response\WalletResponse
     */
    public function getProfile()
    {
        return $this->curl->get(self::API_URL . "/gojek/v2/customer", [], self::buildHeaders())->getResponse();
    }

    /**
	 * getAuthToken
	 * 
	 * @return DefaultResponse
	 */
    public function getBalance()
    {
        return $this->curl->get(self::API_CUSTOMER . "/v1/payment-options/balances", [], self::buildHeaders())->getResponse();
    }

    /**
     * getQrid
     *
     * @param String $phoneNumber
     * @return DefaultResponse|Response\WalletResponse
     */
    public function getQrid($phoneNumber)
    {
        return $this->curl->get(self::API_URL . '/wallet/qr-code?phone_number=' . urlencode($phoneNumber), [], self::buildHeaders())->getResponse();
    }

    /**
	 * getAuthToken
	 * 
	 * @param String			$otpToken
     * @param String            $otpCode
	 * @return DefaultResponse
	 */
    public function getAuthToken($otpToken, $otpCode)
    {
        $payload = array(
            'client_id' => self::clientId,
            'client_secret' => self::clientSecret,
            'data' => array(
                'otp_token' => $otpToken,
                'otp' => $otpCode
            ),
            'grant_type' => 'otp'
        );

        return $this->curl->post(self::API_GOID . '/goid/token', $payload, self::buildHeaders())->getResponse();
    }

    /**
	 * getBankAccountName
	 * 
	 * @param String			$bankCode
     * @param String            $bankNumber
	 * @return DefaultResponse
	 */
    public function getBankAccountName($bankCode, $bankNumber)
    {
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber
        );

        return $this->curl->post(self::API_URL . '/v1/withdrawal/account/validate', $payload, self::buildHeaders())->getResponse();
    }

    /**
     * LoginNumberPhone
     *
     * @param $phoneNumber
     * @return DefaultResponse|Response\WalletResponse
     */
	
    public function loginNumberPhone($phoneNumber)
    {
        $payload = array(
            'client_id' => self::clientId,
            'client_secret' => self::clientSecret,
            'country_code' => '+62',
            'magic_link_ref' => null,
            'phone_number' => self::formatPhone($phoneNumber)
        );
        return $this->curl->post(self::API_GOID . '/goid/login/request', $payload, self::buildHeaders())->getResponse();
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
            'Gojek-Country-Code: ' . self::gojekCountryCode,
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
        
        if (!empty($this->idKey)) {
            array_push($headers, 'Idempotency-Key: ' . $this->idKey);
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
		return $this->curl->delete(self::API_URL . '/v3/auth/token', [], self::buildHeaders())->getResponse();
	}

}
