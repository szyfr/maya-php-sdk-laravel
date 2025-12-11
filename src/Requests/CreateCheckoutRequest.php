<?php

declare(strict_types=1);

namespace Szyfr\Maya\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Szyfr\Maya\Constants\ApiEndpoints;
use Szyfr\Maya\Constants\HttpHeaders;
use Szyfr\Maya\Data\CheckoutResponseData;
use Szyfr\Maya\Data\CreateCheckoutData;

class CreateCheckoutRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly CreateCheckoutData $checkoutData,
        protected readonly string $publicKey,
    ) {}

    public function resolveEndpoint(): string
    {
        return ApiEndpoints::CHECKOUTS;
    }

    protected function defaultHeaders(): array
    {
        return [
            HttpHeaders::AUTHORIZATION => 'Basic '.base64_encode($this->publicKey.':'),
        ];
    }

    protected function defaultBody(): array
    {
        return $this->checkoutData->toArray();
    }

    public function createDtoFromResponse(Response $response): CheckoutResponseData
    {
        $data = $response->json();

        // If the API returns the full checkout details object (with 'id' instead of 'checkoutId'),
        // we need to construct the redirectUrl from the checkout ID
        if (isset($data['id']) && ! isset($data['checkoutId']) && ! isset($data['redirectUrl'])) {
            // Construct the redirect URL from the checkout ID
            // Maya checkout URLs typically follow this pattern
            $checkoutId = $data['id'];
            $redirectUrl = "https://checkout.maya.ph/v1/{$checkoutId}";

            return new CheckoutResponseData(
                checkoutId: $checkoutId,
                redirectUrl: $redirectUrl
            );
        }

        return CheckoutResponseData::fromArray($data);
    }
}
