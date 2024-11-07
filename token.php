<?php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;

if (basename($_SERVER['SCRIPT_FILENAME']) == 'token.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

while (1) {
    
    if (empty($_SESSION['token'])) {
        $bkash = new BkashMerchantAPI;
        if (empty($_SESSION['token_refresh'])) {
            $token = $bkash->grantToken();
        } else {
            $token = $bkash->refreshToken($_SESSION['token_refresh']);
        }
        
            
        if (empty($bkash->getGrantToken())) {
            header('Content-Type: application/json');
            // print_r($token->getResponse());

            echo json_encode([
                'status' => 'payment gateway error'
            ]);
            exit;
        }
            
        $_SESSION['token'] = $bkash->getGrantToken();
        $_SESSION['token_refresh'] = $token->parse()->refresh_token;
        $_SESSION['token_expiration'] = time() + $token->parse()->expires_in;
        
    }

    // refresh if token expired
    if (time() > $_SESSION['token_expiration']) {
        
        unset($_SESSION['token']);
        
        continue;
    }

    break;

}
