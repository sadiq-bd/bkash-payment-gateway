<?php
use Sadiq\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

// token genarate & refresh
require_once __DIR__. '/token.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            margin: 0;
            background: #f5f5f5;
        }
        .payment-container {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #ff4081;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #e6005c;
        }
        @media (max-width: 480px) {
            .payment-container {
                padding: 20px;
            }
            input, button {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Payment</h2>
        <form action="./createpayment.php" method="post">
            <input type="number" name="amount" id="amountInput" step="1" placeholder="Amount" required>
            <input type="text" name="ref" id="refInput" placeholder="Reference" required>
            <button type="submit">Pay with bkash</button>
        </form>
    </div>
</body>
</html>

