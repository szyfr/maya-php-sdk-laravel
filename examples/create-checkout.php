<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Szyfr\Maya\Data\BuyerData;
use Szyfr\Maya\Data\ContactData;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\ItemData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Enums\Environment;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Resources\CheckoutResource;

// Initialize Maya connector
$connector = new MayaConnector(
    environment: Environment::SANDBOX,
    publicKey: 'pk-test-123',
    secretKey: 'sk-test-123'
);

// Create checkout resource
$checkoutResource = new CheckoutResource($connector);

// Prepare checkout data
$checkoutData = new CreateCheckoutData(
    totalAmount: new TotalAmountData(
        value: 100.00,
        currency: 'PHP'
    ),
    redirectUrl: new RedirectUrlData(
        success: 'https://yoursite.com/success',
        failure: 'https://yoursite.com/failure',
        cancel: 'https://yoursite.com/cancel'
    ),
    requestReferenceNumber: 'ORDER-'.time(),
    buyer: new BuyerData(
        firstName: 'Juan',
        lastName: 'Dela Cruz',
        contact: new ContactData(
            phone: '+639171234567',
            email: 'juan@example.com'
        )
    ),
    items: [
        new ItemData(
            name: 'Sample Product',
            quantity: 1,
            amount: new TotalAmountData(100.00, 'PHP')
        ),
    ]
);

try {
    // Create checkout
    $response = $checkoutResource->create($checkoutData);

    echo "Checkout created successfully!\n";
    echo "Checkout ID: {$response->checkoutId}\n";
    echo "Redirect URL: {$response->redirectUrl}\n";
    echo "\nRedirect your customer to the URL above to complete payment.\n";

    // Retrieve checkout details (optional)
    // $details = $checkoutResource->get($response->checkoutId);
    // echo "Status: {$details->status}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
