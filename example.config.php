<?php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;

if (basename($_SERVER['SCRIPT_FILENAME']) == 'config.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';


define('APP_NAME', 'Payment');


########### EXECUTE URL ############
BkashMerchantAPI::setCallBackUrl('http://' . $_SERVER['HTTP_HOST'] . '/executepayment.php');


BkashMerchantAPI::setAppKey('app_key_here');
BkashMerchantAPI::setAppSecret('app_secret_here');
BkashMerchantAPI::setUsername('username_here');
BkashMerchantAPI::setPassword('password_here');


########### ENABLES SANDBOX #################
BkashMerchantAPI::sandBoxMode(true);



########### Log FILE ################
/**
 * To enable log create the log file "gateway.log.txt"
 */
define('log_file', __DIR__ . '/gateway.log.txt');


function prependFileLog($fname, $msg) {
    if (file_exists($fname)) {
        $file = fopen($fname, 'r+');
        $msg = $msg . file_get_contents($fname);
        fwrite($file, $msg, strlen($msg));
        fclose($file);
    }
}




