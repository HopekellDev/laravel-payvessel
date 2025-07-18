# Laravel Payvessel

Laravel Payvessel is a Laravel 10+ package that provides a clean Facade-based integration with the Payvessel API. It allows you to easily create virtual accounts, retrieve account details, and update account BVNs using Laravel's fluent API.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hopekelldev/laravel-payvessel.svg?style=flat-square)](https://packagist.org/packages/hopekelldev/laravel-payvessel)
[![Total Downloads](https://img.shields.io/packagist/dt/hopekelldev/laravel-payvessel.svg?style=flat-square)](https://packagist.org/packages/hopekelldev/laravel-payvessel)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/HopekellDev/laravel-payvessel/main.svg?style=flat-square)](https://scrutinizer-ci.com/g/HopekellDev/laravel-payvessel/?branch=main)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-777BB4.svg?style=flat-square)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-%3E%3D10.0-FF2D20.svg?style=flat-square)](https://laravel.com/)
[![GuzzleHTTP Version](https://img.shields.io/badge/GuzzleHTTP-%3E%3D7.0-3F7E95.svg?style=flat-square)](https://github.com/guzzle/guzzle)

## Requirements
- PHP >= 8.0
- Laravel >= 10.0
- Laravel HTTP Client (built-in from Laravel 7+)

## Installation
Install the package via Composer:

```bash
composer require hopekelldev/laravel-payvessel
```

## Configuration
### Publish Configuration File
Run the following command to publish the configuration file:

```bash
php artisan vendor:publish --tag=config --provider="HopekellDev\Payvessel\PayvesselServiceProvider"
```

### Environment Variables
Add the following to your `.env` file:

```dotenv
PAYVESSEL_API_KEY=your_api_key
PAYVESSEL_API_SECRET=your_api_secret
PAYVESSEL_BUSINESS_ID=your_business_id
PAYVESSEL_API_URL=https://api.payvessel.com
```

## Usage Example
### Create a Virtual Account

```php
use Payvessel;

$response = Payvessel::virtualAccounts()->createVirtualAccount([
    'email' => 'johndoe@example.com',
    'name' => 'JOHN DOE',
    'phoneNumber' => '09012345678',
    'bankcode' => ['999991'], // Example: PalmPay code
    'account_type' => 'STATIC',
    'bvn' => '12345678901', // Or 'nin' => '123456789'
]);

if (isset($response['status']) && $response['status'] === 'success') {
    // Success logic
} else {
    // Handle failure
}
```

## Available Methods
| Category         | Method                                           | Description                            |
|------------------|--------------------------------------------------|----------------------------------------|
| Virtual Accounts | `virtualAccounts()->createVirtualAccount($data)` | Create a reserved virtual account      |
| Virtual Accounts | `virtualAccounts()->getSingleVirtualAccount($account)` | Get virtual account details       |
| Virtual Accounts | `virtualAccounts()->accountBVNUpdate($account, $bvn)` | Update the BVN of a virtual account |

## Example Controller Usage

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HopekellDev\Payvessel\Facades\Payvessel;

class PayvesselController extends Controller
{
    public function createVirtualAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'phoneNumber' => 'required|string',
            'bankcode' => 'required|array',
            'account_type' => 'required|string|in:STATIC,DYNAMIC',
            'bvn' => 'nullable|string',
            'nin' => 'nullable|string',
        ]);

        try {
            $result = Payvessel::virtualAccounts()->createVirtualAccount($validated);
            return response()->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function getVirtualAccount($account): JsonResponse
    {
        try {
            $result = Payvessel::virtualAccounts()->getSingleVirtualAccount($account);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function updateAccountBVN(Request $request, $account): JsonResponse
    {
        $validated = $request->validate([
            'bvn' => 'required|string',
        ]);

        try {
            $result = Payvessel::virtualAccounts()->accountBVNUpdate($account, $validated['bvn']);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
```

## License
This package is released under the MIT License.

## Author
**Ezenwa Hopekell**

- GitHub: [HopekellDev](https://github.com/HopekellDev)
- Email: hopekelltech@gmail.com
- [Message Hopekell on Whatsapp](https://wa.me/message/M3DH3GBDHF35G1)

## Contributions & Issues
Feel free to submit a GitHub Issue or pull request for improvements or bug reports.