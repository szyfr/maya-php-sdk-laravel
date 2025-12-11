<?php

declare(strict_types=1);

namespace Szyfr\Maya\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Szyfr\Maya\Data\WebhookPayloadData;
use Szyfr\Maya\Exceptions\WebhookException;
use Szyfr\Maya\Laravel\Events\WebhookReceived;
use Szyfr\Maya\Webhooks\WebhookSignatureValidator;

class WebhookController extends Controller
{
    public function __construct(
        protected readonly WebhookSignatureValidator $validator,
    ) {}

    /**
     * Handle incoming webhook request
     *
     * @throws \JsonException
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Get raw payload
            $payload = $request->getContent();

            if ($payload === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Empty webhook payload',
                ], 400);
            }

            // Validate signature
            /** @var array<string, array<string>|string> $headers */
            $headers = $request->headers->all();
            $this->validator->validateFromHeaders(
                $payload,
                $headers
            );

            // Parse payload
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook payload format',
                ], 400);
            }

            $webhookPayload = WebhookPayloadData::fromArray($data);

            // Fire Laravel event
            Event::dispatch(new WebhookReceived($webhookPayload, $data));

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
            ]);
        } catch (WebhookException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\JsonException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON payload: '.$e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Webhook processing failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook',
            ], 500);
        }
    }
}
