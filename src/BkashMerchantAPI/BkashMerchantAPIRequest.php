<?php
/**
 * @name: BkashAPI
 * @type: API Handler
 * @namespace: Sadiq\BkashMerchantAPI
 * @author: Sadiq <sadiq.dev.bd@gmail.com>
 */

namespace Sadiq\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;

class BkashMerchantAPIRequest {

    protected $uri = '';

    protected $requestHeaders = [];

    protected $payloads = '';

    protected $errorInfo = '';

    protected $response = '';

    protected $responseHeaders = '';

    public function __construct(string $uri, array $headers, array $payloads) {

        $this->setURI($uri);
        $this->setRequestHeaders($headers);
        $this->setPayloads($this->jsonPayload($payloads));
    
        // additional header
        $this->injectHeader('Content-Type: application/json');

        $fetch = $this->fetch();

        $this->setResponseHeaders($fetch->responseHeaders);
        $this->setResponseBody($fetch->response);
        if ($fetch->error) {
            $this->setErrorInfo($fetch->error);
            throw new BkashMerchantAPIException('BkashMerchantAPIRequest::__construct() : Api Request Error: ' . $this->getErrorInfo());
        }
    }


    public function setURI(string $uri) {
        $this->uri = $uri;
    }

    public function setPayloads($payloads) {
        $this->payloads = $payloads;
    }

    public function setRequestHeaders(array $headers) {
        $this->requestHeaders = $headers;
    }

    public function setResponseHeaders(array $headers) {
        $this->responseHeaders = $headers;
    }

    public function injectPayload(string $payload) {
        $this->payloads[] = $payload;
    }

    public function injectHeader(string $header) {
        $this->requestHeaders[] = $header;
    }


    public function jsonPayload(array $payload) {              
        return json_encode($payload);      
    }


    public function setResponseBody(string $body) {
        $this->response = $body;
    }

    public function setErrorInfo(string $info = null) {

    }

    public function fetch(
        string $url = null, 
        array $headers = [], 
        $postData = null, 
        $method = null, 
        bool $followLoc = true, 
        int $timeout = 10
    ) {
        if ($url === null) $url = $this->uri;
        if (empty($headers)) $headers = $this->requestHeaders;
        if ($postData === null) $postData = $this->payloads;
    
        $curl = curl_init($url);
    
        curl_setopt_array($curl, [
            CURLOPT_HEADER => true,             // include headers in response
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => $followLoc,
            CURLOPT_SSL_VERIFYPEER => false,    // set true if ssl should be validated (cacert.pem is required)
            CURLOPT_TIMEOUT => $timeout,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);
    
        if ($postData !== null && $method === null) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method !== null) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if ($postData !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            }
        }
    
        $response = curl_exec($curl);
    
        $requestHeaders = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        $requestHeaders = explode("\r\n", $requestHeaders);
    
        $info = (object) curl_getinfo($curl);
    
        // Separate headers and body
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $responseHeaders = explode("\r\n", substr($response, 0, $header_size));
        $body = substr($response, $header_size);
    
        $err = curl_error($curl);
        curl_close($curl);
    
    
        return (object) [
            'response' => $body,
            'responseHeaders' => $responseHeaders,
            'requestHeaders' => $requestHeaders,
            'error' => $err,
            'info' => $info,
        ];
    }
    
    public function getResponseHeaders() {
        return $this->responseHeaders;
    }

    public  function getResponseBody() {
        return $this->response;
    }

    public function getErrorInfo() {
        return $this->errorInfo;
    }


}

