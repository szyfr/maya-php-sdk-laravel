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
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\RefundResponseData;

class RefundPaymentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $paymentId,
        protected readonly RefundData $refundData,
        protected readonly string $secretKey,
    ) {}

    public function resolveEndpoint(): string
    {
        return str_replace('{paymentId}', $this->paymentId, ApiEndpoints::PAYMENT_REFUNDS);
    }

    protected function defaultHeaders(): array
    {
        return [
            HttpHeaders::AUTHORIZATION => 'Basic '.base64_encode($this->secretKey.':'),
        ];
    }

    protected function defaultBody(): array
    {
        return $this->refundData->toArray();
    }

    public function createDtoFromResponse(Response $response): RefundResponseData
    {
        return RefundResponseData::fromArray($response->json());
    }
}
