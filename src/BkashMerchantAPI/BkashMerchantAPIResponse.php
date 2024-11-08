<?php
/**
 * @name: BkashAPI
 * @type: API Handler
 * @namespace: Sadiq\BkashMerchantAPI
 * @author: Sadiq <sadiq.dev.bd@gmail.com>
 */

namespace Sadiq\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;
use Sadiq\BkashMerchantAPI\BkashMerchantAPIRequest;

class BkashMerchantAPIResponse {

    protected $response = null;

    protected $json = array();

    public function __construct(BkashMerchantAPIRequest|string $response) {
        if ($response !== null) {
            $this->setResponse($response);
        }
    }

    public function setResponse(BkashMerchantAPIRequest|string $response) {
        $this->response = $response;
    }

    public function getResponse() {
        
        $response = $this->response;
        
        if ($this->response instanceof BkashMerchantAPIRequest) {
            $response = $this->response->getResponseBody();
        }
        
        return $response;
    }

    public function parse() {
        
        $response = $this->getResponse();

        $this->json = json_decode($response, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BkashMerchantAPIException('BkashMerchantAPIResponse::parse() : Invalid Api Response; Invalid JSON: ' . json_last_error_msg());
        }
        
        return (object) $this->json;
    }

    public function asObj() {
        
        $this->parse();

        return (object) $this->json;
        
    }

    public function asArray() {
        
        $this->parse();

        return (array) $this->json;

    }

}

