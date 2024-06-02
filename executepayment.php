<?php
use Sadiq\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// token genarate & refresh
require_once __DIR__. '/token.php';

header('Content-Type: application/json');


$requires = [
    'paymentID', 'status'
];
foreach ($requires as $req) {
    if (empty($_GET[$req])) {
        exit(json_encode(['message' => $req.' is missing']));
    }
}


$token = $_SESSION['token'];
$paymentID = $_GET['paymentID'];
$status = $_GET['status'];


$bkash = new BkashAPI;
if (empty((
            $resp = $bkash
               ->setGrantToken($token)
               ->executePayment($paymentID)
          )->jsonObj()->errorCode)
   ) {
    
    // $query = $bkash->queryPayment($paymentID);

    // log execute payment resp
    prependFileLog(log_file, "\n\n- Execute Payment\n{$resp->response()}\n\n");


    // log query payment resp
    // prependFileLog(log_file, "\n\n- Query Payment\n{$query->response()}\n\n");


    if (@$resp->jsonObj()->transactionStatus == 'Completed') {
        header('Location: /success.html');
        exit;
    }

}

echo json_encode(['status' => 'error']);


