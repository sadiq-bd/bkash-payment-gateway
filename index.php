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
    <title>Merchant Payment</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            margin: 0;
            background-color: #fff; /* Default background color */
            color: #333; /* Default text color */
            overflow: hidden;
        }
        .payment-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            animation: slideIn 1s ease-in-out;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 28px;
        }
        input, button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f8f8f8;
            color: #333;
        }
        input::placeholder {
            color: #999;
        }
        button {
            background-color: #f39c12;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        button span {
	    color:#fff;
            position: relative;
            z-index: 1;
        }
        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.6s ease;
            z-index: 0;
            transform: translate(-50%, -50%);
        }
        button:hover::before {
            width: 0;
            height: 0;
        }
        button:hover {
            background-color: #e67e22;
            transform: translateY(-2px) scale(1.05);
        }
        button:active {
            transform: translateY(0);
        }
        @media (max-width: 480px) {
            .payment-container {
                padding: 20px;
            }
            input, button {
                padding: 12px;
                font-size: 14px;
            }
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #f1c40f;
            animation: confetti 4s linear infinite;
            transform-origin: top left;
        }
        @keyframes confetti {
            0% { 
                transform: translateY(-100vh) rotate(0deg); 
            }
            100% { 
                transform: translateY(100vh) rotate(360deg); 
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Payment</h2>
        <form action="./createpayment.php" method="post">
            <input type="number" name="amount" id="amountInput" step="0.01" placeholder="Amount" required>
            <input type="text" name="ref" id="refInput" placeholder="Reference" required>
            <button type="submit"><span>Pay with bkash</span></button>
        </form>
    </div>
    <script>
        // Generate confetti
        for (let i = 0; i < 15; i++) {
            const confetti = document.createElement('div');
            confetti.classList.add('confetti');
            confetti.style.left = `${Math.random() * 100}vw`;
            confetti.style.animationDelay = `${Math.random() * 4}s`;
            document.body.appendChild(confetti);
        }
    </script>
</body>
</html>
