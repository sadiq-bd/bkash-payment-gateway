<p align="left">
  <img src="https://api.sadiq.workers.dev/app/github/repo/bkash-payment-gateway/views" alt="View Counter" />
</p>

# bKash Payment Gateway PHP

A well-structured and secure PHP library to integrate bKash payment processing into your application. Provides robust API handling for bKash Merchant operations.

---

## Features

- Easy integration with bKash Merchant API
- Handles payment creation, execution, query, refund, and more
- Structured request and response handling
- Exception handling for API errors
- Sandbox and production mode switch
- Example scripts included

---

## Installation

```bash
composer require sadiq-bd/bkash-payment-gateway
```

---

## Configuration

Set up your credentials and environment using the provided static setter methods.
You can use the sample `example.config.php` file as a template.

```php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;

// Set callback URL (for execute payment)
BkashMerchantAPI::setCallBackUrl('https://yourdomain.com/executepayment.php');

// Set your bKash credentials
BkashMerchantAPI::setAppKey('your_app_key');
BkashMerchantAPI::setAppSecret('your_app_secret');
BkashMerchantAPI::setUsername('your_username');
BkashMerchantAPI::setPassword('your_password');

// Enable sandbox mode (set to false for production)
BkashMerchantAPI::sandBoxMode(true);
```

---

## Usage

### Token Generation

Before creating a payment, you need to generate a grant token. See `token.php` and `example.config.php` for sample token generation logic.

### Creating a Payment

Below is an example based on the actual `createpayment.php` flow:

```php
use Sadiq\BkashMerchantAPI\BkashMerchantAPI;
use Sadiq\BkashMerchantAPI\Exception\BkashMerchantAPIException;

// Assume configuration and token generation (see example.config.php and token.php)
session_start();
$token = $_SESSION['token'];

$amount = 100; // BDT
$invoice = strtoupper(uniqid());
$reference = "CustomerReference";

try {
    $bkash = new BkashMerchantAPI;
    $bkash->setGrantToken($token);
    if ($resp = $bkash->createPayment($amount, $invoice, $reference)) {
        // Optional: log response
        // prependFileLog(log_file, "\n\n- Create Payment\n{$resp->getResponse()}\n\n");
        $bkash->redirectToPayment($resp);
    }
} catch (BkashMerchantAPIException $e) {
    die($e->getMessage());
}
```

- The user will be redirected to bKash for payment approval.

### Executing a Payment

After user approval, bKash will redirect back to your callback URL (e.g. `executepayment.php`). That file will handle execution.

---

## Example Directory Structure

- `src/BkashMerchantAPI/` — Core library classes
- `public/` — Example entry points / demo scripts (`createpayment.php`, `executepayment.php`, `index.php`, etc.)
- `example.config.php` — Example configuration file
- `token.php` — Example for grant token management
- `README.md` — Documentation
- `composer.json` — Package definition

---

## Exception Handling

All API errors throw `BkashMerchantAPIException` for robust error management.

---

## Logging

To enable logging, create a file named `gateway.log.txt` in your project root.
Logging is supported via the `prependFileLog` function (see `example.config.php`).

---

## Security

- Never expose your credentials in public repositories.
- Always use HTTPS.
- Rotate credentials regularly.

---

## License

[MIT](LICENSE)

---

## Support

For issues and feature requests, please use the [GitHub Issues](https://github.com/sadiq-bd/bkash-payment-gateway/issues) page.

---

## More Information

- [bKash Official API Documentation](https://developer.bkash.com/)
- [Packagist Package](https://packagist.org/packages/sadiq-bd/bkash-payment-gateway)
- [Browse All Files in This Repo](https://github.com/sadiq-bd/bkash-payment-gateway/search)
