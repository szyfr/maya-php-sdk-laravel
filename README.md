# Maya PHP SDK for Laravel
A modern PHP SDK for integrating Maya Checkout payment gateway, built with [Saloon HTTP client](https://docs.saloon.dev/) and type-safe Data objects. Designed for Laravel 12+ with full support for webhooks, refunds, and comprehensive testing.

## Features

✅ **Type-Safe Data Objects** - Full type safety with PHP 8.2+ and comprehensive data objects  
✅ **Saloon HTTP Client** - Modern, elegant HTTP client with excellent testing support  
✅ **Laravel 12 Integration** - Service provider, facade, and auto-discovery  
✅ **Webhook Signature Validation** - Secure HMAC SHA256 webhook verification  
✅ **Comprehensive Testing** - PEST test suite with 100% coverage  
✅ **Code Quality Tools** - Pint, Rector, and PHPStan configured  
✅ **Sandbox & Production** - Easy environment switching

## Requirements

- PHP 8.2 or higher
- Laravel 12.0 or higher
- Composer

## Installation

Install the package via Composer:

```bash
composer require szyfr/maya-php-sdk-laravel
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=maya-config
```

This will create `config/maya.php` where you can customize your settings.

### Environment Variables

Add your Maya API credentials to `.env`:

```env
MAYA_ENVIRONMENT=sandbox
MAYA_PUBLIC_KEY=pk-your-public-key-here
MAYA_SECRET_KEY=sk-your-secret-key-here

# Optional: Configure redirect URLs
MAYA_REDIRECT_URL_SUCCESS=https://yourapp.com/payment/success
MAYA_REDIRECT_URL_FAILURE=https://yourapp.com/payment/failure
MAYA_REDIRECT_URL_CANCEL=https://yourapp.com/payment/cancel

# Optional: Webhook configuration
MAYA_WEBHOOK_ENABLED=true
MAYA_WEBHOOK_PATH=webhooks/maya
```

Get your API keys from the [Maya Developer Hub](https://developers.maya.ph).

## Quick Start

### Creating a Checkout

```php
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Laravel\Facades\Maya;
use Szyfr\Maya\Laravel\Helpers\RedirectUrlHelper;

$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(500.00, 'PHP'),
    redirectUrl: RedirectUrlHelper::fromConfig(), // Uses config('maya.redirect_urls')
    requestReferenceNumber: 'ORDER-' . uniqid()
);

$response = Maya::create($checkoutData);

// Redirect user to Maya checkout page
return redirect($response->redirectUrl);
```

### Using Custom Redirect URLs

You can also specify custom redirect URLs instead of using the config:

```php
use Szyfr\Maya\Data\RedirectUrlData;

$redirectUrl = new RedirectUrlData(
    success: route('payment.success'),
    failure: route('payment.failure'),
    cancel: route('payment.cancel')
);

$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(500.00, 'PHP'),
    redirectUrl: $redirectUrl,
    requestReferenceNumber: 'ORDER-' . uniqid()
);
```

### Retrieving Checkout Details

```php
$details = Maya::get($checkoutId);

if ($details->isPaid) {
    // Payment completed
    echo "Payment Status: {$details->status}";
}
```

### Processing Refunds

```php
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\TotalAmountData;

$refundData = new RefundData(
    totalAmount: new TotalAmountData(100.00, 'PHP'),
    reason: 'Customer requested refund'
);

$refund = Maya::refund($paymentId, $refundData);
```

## Webhook Handling

The package automatically registers a webhook endpoint at `/webhooks/maya` (configurable).

### Listening to Webhook Events

Create a listener for the `WebhookReceived` event:

```php
// app/Listeners/ProcessMayaPayment.php
use Szyfr\Maya\Laravel\Events\WebhookReceived;

class ProcessMayaPayment
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        if ($payload->isPaid) {
            // Update your order status
            Order::where('reference_number', $payload->requestReferenceNumber)
                ->update(['status' => 'paid']);
        }
    }
}
```

Register the listener in `EventServiceProvider`:

```php
protected $listen = [
    WebhookReceived::class => [
        ProcessMayaPayment::class,
    ],
];
```

### Webhook Configuration

Customize webhook settings in `config/maya.php`:

```php
'webhook' => [
    'enabled' => true,
    'route_path' => 'webhooks/maya',
    'middleware' => ['api'],
],
```

## Advanced Usage

### Using Dependency Injection

```php
use Szyfr\Maya\Resources\CheckoutResource;

class PaymentController extends Controller
{
    public function __construct(
        protected CheckoutResource $maya
    ) {}

    public function createCheckout(Request $request)
    {
        $response = $this->maya->create($checkoutData);
        return response()->json($response);
    }
}
```

### Adding Buyer Information

```php
use Szyfr\Maya\Data\BuyerData;
use Szyfr\Maya\Data\ContactData;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Laravel\Helpers\RedirectUrlHelper;

$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(500.00, 'PHP'),
    redirectUrl: RedirectUrlHelper::fromConfig(),
    requestReferenceNumber: 'ORDER-123',
    buyer: new BuyerData(
        firstName: 'Juan',
        lastName: 'Dela Cruz',
        contact: new ContactData(
            phone: '+639171234567',
            email: 'juan@example.com'
        )
    )
);
```

### Adding Line Items

```php
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\ItemData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Laravel\Helpers\RedirectUrlHelper;

$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(500.00, 'PHP'),
    redirectUrl: RedirectUrlHelper::fromConfig(),
    requestReferenceNumber: 'ORDER-123',
    items: [
        new ItemData(
            name: 'Product 1',
            quantity: 2,
            amount: new TotalAmountData(250.00, 'PHP')
        ),
    ]
);
```

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

## Code Quality

The package includes several code quality tools:

```bash
# Format code with Pint
composer format

# Check code style
composer format-test

# Run PHPStan static analysis
composer analyse

# Run Rector refactoring
composer refactor-dry
```

## Vanilla PHP Usage

You can use this package without Laravel:

```php
use Szyfr\Maya\Enums\Environment;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Resources\CheckoutResource;

$connector = new MayaConnector(
    environment: Environment::SANDBOX,
    publicKey: 'pk-your-key',
    secretKey: 'sk-your-key'
);

$checkoutResource = new CheckoutResource($connector);
$response = $checkoutResource->create($checkoutData);
```

## Exception Handling

The SDK throws specific exceptions for different error scenarios:

```php
use Szyfr\Maya\Exceptions\AuthenticationException;
use Szyfr\Maya\Exceptions\ValidationException;
use Szyfr\Maya\Exceptions\MayaException;

try {
    $response = Maya::create($checkoutData);
} catch (AuthenticationException $e) {
    // Invalid API credentials
} catch (ValidationException $e) {
    // Validation errors from Maya API
} catch (MayaException $e) {
    // Other Maya API errors
}
```

## Configuration Reference

See `config/maya.php` for all available configuration options:

- `environment` - Sandbox or production
- `public_key` - Your Maya public API key
- `secret_key` - Your Maya secret API key
- `webhook.enabled` - Enable/disable webhook routes
- `webhook.route_path` - Webhook endpoint path
- `webhook.middleware` - Middleware for webhook route

## Resources

- [Maya Developer Hub](https://developers.maya.ph)
- [Maya Checkout Documentation](https://developers.maya.ph/docs/checkout)
- [Saloon Documentation](https://docs.saloon.dev/)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- Built with [Saloon](https://docs.saloon.dev/)
- Tested with [PEST](https://pestphp.com/)
- Code quality by [Pint](https://laravel.com/docs/pint), [Rector](https://getrector.com/), and [PHPStan](https://phpstan.org/)
