<?php
use Sadiq\BkashAPI;
if (basename($_SERVER['SCRIPT_FILENAME']) == 'config.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';


define('APP_NAME', 'Payment');


########### EXECUTE URL ############
BkashAPI::setCallBackUrl('http://' . $_SERVER['HTTP_HOST'] . '/executepayment.php');


BkashAPI::setAppKey('app_key_here');
BkashAPI::setAppSecret('app_secret_here');
BkashAPI::setUsername('username_here');
BkashAPI::setPassword('password_here');


########### SANDBOX #################
BkashAPI::setApiBaseURL('https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/');


########### PRODUCTION ##############
# BkashAPI::setApiBaseURL('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/');



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




