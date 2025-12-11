<?php

declare(strict_types=1);

namespace Szyfr\Maya\Resources;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Szyfr\Maya\Data\CheckoutDetailsData;
use Szyfr\Maya\Data\CheckoutResponseData;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\RefundResponseData;
use Szyfr\Maya\Exceptions\AuthenticationException;
use Szyfr\Maya\Exceptions\MayaException;
use Szyfr\Maya\Exceptions\ValidationException;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Requests\CreateCheckoutRequest;
use Szyfr\Maya\Requests\GetCheckoutRequest;
use Szyfr\Maya\Requests\RefundPaymentRequest;

class CheckoutResource
{
    public function __construct(
        protected readonly MayaConnector $connector,
    ) {}

    /**
     * Create a new checkout
     *
     * @param CreateCheckoutData $data The checkout data including total amount, redirect URLs, and optional buyer/items
     *
     * @return CheckoutResponseData Contains the checkout ID and redirect URL for payment
     *
     * @throws MayaException When the API returns an error
     * @throws AuthenticationException When API credentials are invalid (401/403)
     * @throws ValidationException When request data is invalid (422)
     */
    public function create(CreateCheckoutData $data): CheckoutResponseData
    {
        try {
            $request = new CreateCheckoutRequest($data, $this->connector->getPublicKey());
            $response = $this->connector->send($request);

            return $request->createDtoFromResponse($response);
        } catch (FatalRequestException|RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get checkout details by ID
     *
     * @param string $checkoutId The checkout ID to retrieve
     *
     * @return CheckoutDetailsData Contains full checkout details including status, payment info, and items
     *
     * @throws MayaException When the API returns an error
     * @throws AuthenticationException When API credentials are invalid (401/403)
     */
    public function get(string $checkoutId): CheckoutDetailsData
    {
        try {
            $request = new GetCheckoutRequest($checkoutId, $this->connector->getSecretKey());
            $response = $this->connector->send($request);

            return $request->createDtoFromResponse($response);
        } catch (FatalRequestException|RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Refund a payment
     *
     * @param string $paymentId The payment ID to refund
     * @param RefundData $data The refund data including amount and reason
     *
     * @return RefundResponseData Contains the refund ID, status, and details
     *
     * @throws MayaException When the API returns an error
     * @throws AuthenticationException When API credentials are invalid (401/403)
     * @throws ValidationException When refund data is invalid (422)
     */
    public function refund(string $paymentId, RefundData $data): RefundResponseData
    {
        try {
            $request = new RefundPaymentRequest($paymentId, $data, $this->connector->getSecretKey());
            $response = $this->connector->send($request);

            return $request->createDtoFromResponse($response);
        } catch (FatalRequestException|RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handle Saloon exceptions and convert to Maya exceptions
     *
     * @throws MayaException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    protected function handleException(FatalRequestException|RequestException $e): never
    {
        // @phpstan-ignore-next-line - Saloon exceptions have getResponse() method
        $response = $e->getResponse();
        // @phpstan-ignore-next-line - Response can be null in some cases
        $statusCode = $response?->status() ?? 500;
        // @phpstan-ignore-next-line - Response can be null in some cases
        $responseBody = $response?->json() ?? [];

        match ($statusCode) {
            401, 403 => throw AuthenticationException::invalidCredentials(),
            422 => $this->handleValidationError($responseBody),
            default => throw MayaException::fromResponse($responseBody, $statusCode),
        };
    }

    /**
     * Handle validation errors from Maya API
     * The API can return errors in different formats:
     * - { "errors": [...] } format
     * - { "parameters": [...] } format with field/description
     */
    protected function handleValidationError(array $responseBody): never
    {
        // Handle Maya API format with "parameters" array
        if (isset($responseBody['parameters']) && is_array($responseBody['parameters'])) {
            $errors = [];
            foreach ($responseBody['parameters'] as $param) {
                if (isset($param['field']) && isset($param['description'])) {
                    $errors[$param['field']] = [$param['description']];
                }
            }
            throw ValidationException::fromErrors($errors);
        }

        // Handle standard errors format
        throw ValidationException::fromErrors($responseBody['errors'] ?? []);
    }
}
