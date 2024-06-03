<?php
use Sadiq\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

// Token generation & refresh
require_once __DIR__ . '/token.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
            width: 400px; /* Increased width */
        }
        h2 {
            margin-bottom: 20px;
            font-size: 28px;
        }
        .input-container {
            position: relative;
            width: 100%;
            margin: 20px 0;
        }
        input {
            width: calc(100% - 30px);
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f8f8f8;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus {
            border-color: #f39c12;
            box-shadow: 0 0 8px rgba(243, 156, 18, 0.6);
            outline: none;
        }
        label {
            position: absolute;
            left: 15px;
            top: 15px;
            background-color: #f8f8f8;
            padding: 0 5px;
            color: #999;
            pointer-events: none;
            transition: top 0.3s ease, font-size 0.3s ease;
        }
        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: #888;
            font-weight: 550;
            background: linear-gradient(to bottom, #fff, #f8f8f8);
        }
        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f39c12;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        button span {
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
            background-color: rgba(255, 255, 255, 0.3);
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
                width: 90%; /* Responsive width */
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
            border-radius: 50%;
            background-color: #f1c40f;
            animation: confetti 4s linear infinite;
            transform-origin: top left;
        }
        @keyframes confetti {
            0% { 
                transform: translateY(-100vh); 
            }
            100% { 
                transform: translateY(100vh); 
            }
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: visible;
        }
        .loading-dots {
            display: flex;
            gap: 10px;
        }
        .loading-dots div {
            width: 15px;
            height: 15px;
            background-color: #f39c12;
            border-radius: 50%;
            animation: loading 0.6s infinite alternate;
        }
        .loading-dots div:nth-child(2) {
            animation-delay: 0.2s;
        }
        .loading-dots div:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes loading {
            from {
                opacity: 0.3;
                transform: translateY(0);
            }
            to {
                opacity: 1;
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2><?= APP_NAME ?></h2>
        <form id="paymentForm" action="./createpayment.php" method="post">
            <div class="input-container">
                <input type="number" name="amount" id="amountInput" step="1" min="1" placeholder=" " value="<?= isset($_GET['amount']) && filter_var($_GET['amount'], FILTER_VALIDATE_INT) ? $_GET['amount'] : '' ?>" required>
                <label for="amountInput">Amount</label>
            </div>
            <div class="input-container">
                <input type="text" name="ref" id="refInput" placeholder=" " value="<?= isset($_GET['ref']) ? $_GET['ref'] : '' ?>" required>
                <label for="refInput">Reference</label>
            </div>
            <button type="submit"><span>Pay with bkash</span></button>
        </form>
    </div>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-dots">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <script>
        // Generate confetti
        for (let i = 0; i < 15; i++) {
            const confetti = document.createElement('div');
            confetti.classList.add('confetti');
            confetti.style.left = `${Math.random() * 100}vw`;
            confetti.style.top = '10px';
            confetti.style.animationDelay = `${Math.random() * 4}s`;
            document.body.appendChild(confetti);
        }

        const form = document.getElementById('paymentForm');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Show loading animation on form submit
        form.addEventListener('submit', function(event) {
            loadingOverlay.style.visibility = 'visible';
            setTimeout(() => {
                loadingOverlay.style.visibility = 'hidden';
            }, 5000);
        });

        window.addEventListener('DOMContentLoaded', (e) => {
            setTimeout(() => {
                loadingOverlay.style.visibility = 'hidden';
            }, 1000);
        });
    </script>
</body>
</html>
