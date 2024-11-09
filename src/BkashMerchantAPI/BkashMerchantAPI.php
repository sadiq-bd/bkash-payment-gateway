<?php
/**
 * @name: BkashAPI
 * @type: API Handler
 * @namespace: Sadiq\BkashMerchantAPI
 * @author: Sadiq <sadiq.dev.bd@gmail.com>
 */

namespace Sadiq\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;

class BkashMerchantAPI {

    private static $isSandBox = false;
    private static $baseURL = '';
    private static $tokenURL = '';
    private static $createURL = '';
    private static $executeURL = '';
    private static $queryURL = '';
    private static $refreshTokenURL = '';
    private static $refundURL = '';
    private static $refundStatusURL = '';
    private static $searchURL = '';

    private static $callBackURL = '';

    private static $app_key = '';
    private static $app_secret = '';
    private static $username = '';
    private static $password = '';

    private $grantToken = '';

    const PRODUCTION_URL = 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/';
    const SANDBOX_URL = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/';

    public function __construct() {

        if (self::$isSandBox) {
            self::setApiBaseURL(self::SANDBOX_URL);
        } else {
            self::setApiBaseURL(self::PRODUCTION_URL);      // default
        }    
        
        self::setTokenGrantURL('/checkout/token/grant');
        self::setRefreshTokenURL('/checkout/token/refresh');
        self::setCreatePaymentURL('/checkout/create');
        self::setExecutePaymentURL('/checkout/execute');
        self::setQueryPaymentURL('/checkout/payment/status');
        self::setRefundURL('/checkout/payment/refund');
        self::setRefundStatusURL('/checkout/payment/refund');
        self::setSearchTransactionURL('/checkout/general/searchTransaction');

    }

    public static function setApiBaseURL(string $value) {
        self::$baseURL = $value;
    }
       

    public static function setTokenGrantURL(string $value) {
        self::$tokenURL = self::makeApiURL($value);
    }
    

    public static function setCreatePaymentURL(string $value) {
        self::$createURL = self::makeApiURL($value);
    }
    

    public static function setExecutePaymentURL(string $value) {
        self::$executeURL = self::makeApiURL($value);
    }
 
    public static function setQueryPaymentURL(string $value) {
        self::$queryURL = self::makeApiURL($value);
    }
     
    public static function setRefreshTokenURL(string $value) {
        self::$refreshTokenURL = self::makeApiURL($value);
    }
    
  
    public static function setRefundURL(string $value) {
        self::$refundURL = self::makeApiURL($value);
    }
    
  
    public static function setRefundStatusURL(string $value) {
        self::$refundStatusURL = self::makeApiURL($value);
    }
    
  
    public static function setSearchTransactionURL(string $value) {
        self::$searchURL = self::makeApiURL($value);
    }

    private static function makeApiURL(string $path) {
        return rtrim(self::$baseURL, '/') . '/' . ltrim($path, '/');
    }
 
 
    public static function setCallBackUrl(string $value) {
        self::$callBackURL = $value;
    }


    public static function setAppKey(string $value) {
        self::$app_key = $value;
    }

    public static function setAppSecret(string $value) {
        self::$app_secret = $value;
    }


    public static function setUsername(string $value) {
        self::$username = $value;
    }


    public static function setPassword(string $value) {
        self::$password = $value;
    }

    public function setGrantToken(string $token) {
        $this->grantToken = $token;
        return $this;
    }

    public function getGrantToken() {
        return $this->grantToken;
    }

    public function grantToken() {

        $tokenRequest = new BkashMerchantAPIRequest(
            self::$tokenURL, 
            array(
                'username: ' . self::$username,
                'password: ' . self::$password
            ), array(
                'app_key' => self::$app_key,
                'app_secret' => self::$app_secret
            )
        );

        $response = new BkashMerchantAPIResponse($tokenRequest);

        if (!empty($response->parse()->id_token)) {
            $this->setGrantToken($response->parse()->id_token);
        }

        return $response;
    }

    
    public function refreshToken(string $refrshTokenValue) {

        $tokenRequest = new BkashMerchantAPIRequest(
            self::$refreshTokenURL, 
            array(
                'username: ' . self::$username,
                'password: ' . self::$password
            ), array(
                'app_key' => self::$app_key,
                'app_secret' => self::$app_secret,
                'refresh_token' => $refrshTokenValue
            )
        );

        $response = new BkashMerchantAPIResponse($tokenRequest);

        if (!empty($response->parse()->id_token)) {
            $this->setGrantToken($response->parse()->id_token);
        }

        return $response;
    }


    public function createPayment(string|int $amount, string $invoice, string $ref, string $intent = 'sale') {
        
        $createRequest = new BkashMerchantAPIRequest(
            self::$createURL, 
            $this->createAuthHeaders(), 
            array(
                'mode' => '0011',
                'payerReference' => $ref,
                'callbackURL' => self::$callBackURL,
                'amount' => $amount, 
                'currency' => 'BDT', 
                'merchantInvoiceNumber' => $invoice,
                'intent' => $intent
            )
        );

        return new BkashMerchantAPIResponse($createRequest);

    }

    public function executePayment(string $paymentID) {
        
        $executeRequest = new BkashMerchantAPIRequest(
            self::$executeURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID
            )
        );

        return new BkashMerchantAPIResponse($executeRequest);

    }

    
    public function queryPayment(string $paymentID) {
        
        $queryRequest = new BkashMerchantAPIRequest(
            self::$queryURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID
            )
        );

        return new BkashMerchantAPIResponse($queryRequest);

    }


    public function refundPayment(string $paymentID, string|int $amount, string $trxID, string $sku, string $reason) {
        
        $refundRequest = new BkashMerchantAPIRequest(
            self::$refundURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID,
                'amount' => $amount,
                'trxID' => $trxID,
                'sku' => $sku,
                'reason' => $reason
            )
        );

        return new BkashMerchantAPIResponse($refundRequest);

    }

    public function refundStatus(string $paymentID, string $trxID) {
        
        $refundStatusRequest = new BkashMerchantAPIRequest(
            self::$refundStatusURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID,
                'trxID' => $trxID
            )
        );

        return new BkashMerchantAPIResponse($refundStatusRequest);

    }

    public function searchTransaction(string $trxID) {
        
        $searchRequest = new BkashMerchantAPIRequest(
            self::$searchURL, 
            $this->createAuthHeaders(), 
            array(
                'trxID' => $trxID
            )
        );

        return new BkashMerchantAPIResponse($searchRequest);
    }

    public function redirectToPayment(BkashMerchantAPIResponse $createResponse) {

        $createResponse = $createResponse->parse();

        header('HTTP/1.1 302 Found');
        header('Location: ' . $createResponse->bkashURL);

        exit();
    }

    public function isPaymentSuccess(BkashMerchantAPIResponse $executeResponse, string $invoice) {

        $executeResponse = $executeResponse->parse();

        return !empty($executeResponse->transactionStatus) && strtolower($executeResponse->transactionStatus) == 'completed' && $executeResponse->merchantInvoiceNumber == $invoice;
        
    }

    private function createAuthHeaders() {
        return array(
            'authorization: '. $this->grantToken,
            'x-app-key: '. self::$app_key    
        );
    }

    public static function sandBoxMode(bool $sandbox = true) {
        self::$isSandBox = $sandbox;
    }

}

