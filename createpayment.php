<?php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;

require_once __DIR__ . '/config.php';
session_start();

// token genarate & refresh
require_once __DIR__. '/token.php';

header('Content-Type: application/json');

$requires = [
    'amount', 'ref'
];
foreach ($requires as $req) {
    if (empty($_POST[$req])) {
        exit(json_encode(['message' => $req.' is missing']));
    }
}


$token = $_SESSION['token'];

$amount = $_POST['amount'];

$invoice = strtoupper(uniqid());

$_SESSION['invoice'] = $invoice;

$reference = strlen($_POST['ref']) > 50 ? substr($_POST['ref'], 0, 50) : $_POST['ref'];

try {

    $bkash = new BkashMerchantAPI;
    $bkash->setGrantToken($token);
    if ($resp = $bkash->createPayment($amount, $invoice, $reference)) {
        
        // log create payment resp
        prependFileLog(log_file, "\n\n- Create Payment\n{$resp->getResponse()}\n\n");

        $bkash->redirectToPayment($resp);  
        
    }

} catch (BkashMerchantAPIException $e) {
    die ($e->getMessage());
}

