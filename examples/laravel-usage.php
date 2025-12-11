<?php

declare(strict_types=1);

/**
 * Laravel Usage Examples for Maya PHP SDK
 */

use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Laravel\Events\WebhookReceived;
use Szyfr\Maya\Laravel\Facades\Maya;
use Szyfr\Maya\Resources\CheckoutResource;

// ============================================
// Example 1: Using the Facade
// ============================================

// Create a checkout using the facade
$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(500.00, 'PHP'),
    redirectUrl: new RedirectUrlData(
        success: route('payment.success'),
        failure: route('payment.failure'),
        cancel: route('payment.cancel')
    ),
    requestReferenceNumber: 'ORDER-' . uniqid()
);

$response = Maya::create($checkoutData);

// Redirect user to Maya checkout page
return redirect($response->redirectUrl);

// ============================================
// Example 2: Using Dependency Injection
// ============================================

class PaymentController extends Controller
{
    public function __construct(
        protected CheckoutResource $maya
    ) {
    }

    public function createCheckout(Request $request)
    {
        $checkoutData = new CreateCheckoutData(
            totalAmount: new TotalAmountData(
                value: $request->input('amount'),
                currency: 'PHP'
            ),
            redirectUrl: new RedirectUrlData(
                success: route('payment.success'),
                failure: route('payment.failure'),
                cancel: route('payment.cancel')
            ),
            requestReferenceNumber: 'ORDER-' . $request->input('order_id')
        );

        $response = $this->maya->create($checkoutData);

        return response()->json([
            'checkout_id' => $response->checkoutId,
            'redirect_url' => $response->redirectUrl,
        ]);
    }

    public function checkStatus(string $checkoutId)
    {
        $details = $this->maya->get($checkoutId);

        return response()->json([
            'status' => $details->status,
            'is_paid' => $details->isPaid,
        ]);
    }

    public function refundPayment(Request $request, string $paymentId)
    {
        $refundData = new RefundData(
            totalAmount: new TotalAmountData(
                value: $request->input('amount'),
                currency: 'PHP'
            ),
            reason: $request->input('reason', 'Customer requested refund')
        );

        $response = $this->maya->refund($paymentId, $refundData);

        return response()->json([
            'refund_id' => $response->id,
            'status' => $response->status,
        ]);
    }
}

// ============================================
// Example 3: Handling Webhooks with Events
// ============================================

// In your EventServiceProvider.php
protected $listen = [
    WebhookReceived::class => [
        ProcessMayaPayment::class,
    ],
];

// Create a listener: app/Listeners/ProcessMayaPayment.php
class ProcessMayaPayment
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        if ($payload->isPaid) {
            // Update order status in database
            Order::where('reference_number', $payload->requestReferenceNumber)
                ->update([
                    'status' => 'paid',
                    'payment_id' => $payload->id,
                    'paid_at' => now(),
                ]);

            // Send confirmation email, etc.
        }
    }
}

// ============================================
// Example 4: Configuration in .env
// ============================================

/*
MAYA_ENVIRONMENT=sandbox
MAYA_PUBLIC_KEY=pk-your-public-key
MAYA_SECRET_KEY=sk-your-secret-key
MAYA_WEBHOOK_ENABLED=true
MAYA_WEBHOOK_PATH=webhooks/maya
*/

// ============================================
// Example 5: Publishing Configuration
// ============================================

/*
php artisan vendor:publish --tag=maya-config

This will create config/maya.php where you can customize:
- Environment (sandbox/production)
- API keys
- Webhook settings
*/
