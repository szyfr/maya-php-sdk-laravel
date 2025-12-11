<?php

declare(strict_types=1);

namespace Szyfr\Maya\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Szyfr\Maya\Constants\ApiEndpoints;
use Szyfr\Maya\Constants\HttpHeaders;
use Szyfr\Maya\Data\CheckoutDetailsData;

class GetCheckoutRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $checkoutId,
        protected readonly string $secretKey,
    ) {}

    public function resolveEndpoint(): string
    {
        return str_replace('{id}', $this->checkoutId, ApiEndpoints::CHECKOUT_BY_ID);
    }

    protected function defaultHeaders(): array
    {
        return [
            HttpHeaders::AUTHORIZATION => 'Basic '.base64_encode($this->secretKey.':'),
        ];
    }

    public function createDtoFromResponse(Response $response): CheckoutDetailsData
    {
        return CheckoutDetailsData::fromArray($response->json());
    }
}
