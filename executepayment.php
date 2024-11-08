<?php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;

require_once __DIR__ . '/config.php';
session_start();

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


try {

    $bkash = new BkashMerchantAPI;
    $bkash->setGrantToken($token);

    if (empty(
        ($resp = $bkash->executePayment($paymentID))->parse()->errorCode
    )) {
        
        // $query = $bkash->queryPayment($paymentID);

        // log execute payment resp
        prependFileLog(log_file, "\n\n- Execute Payment\n{$resp->getResponse()}\n\n");


        // log query payment resp
        // prependFileLog(log_file, "\n\n- Query Payment\n{$query->getResponse()}\n\n");


        if ($bkash->isPaymentSuccess($resp, $_SESSION['invoice'])) {

            unset($_SESSION['invoice']);

            header('Location: /success.html');
            exit;

        }

    }

    echo json_encode(['status' => 'error']);


} catch (BkashMerchantAPIException $e) {
    die ($e->getMessage());
}
