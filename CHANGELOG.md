# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2024-12-09

### Added
- Initial release of Maya PHP SDK for Laravel
- Saloon HTTP client integration for API requests
- Type-safe DTO for all request and response data
- Support for Maya Checkout API operations:
  - Create checkout
  - Retrieve checkout details
  - Process refunds
- Webhook signature validation with HMAC SHA256
- Laravel 12 service provider with auto-discovery
- Laravel facade for easy access
- Webhook controller with automatic signature validation
- `WebhookReceived` event for Laravel event listeners
- Comprehensive PEST test suite
- Code quality tools: Pint, Rector, PHPStan
- Support for both sandbox and production environments
- Detailed documentation and usage examples
- Exception handling for authentication, validation, and API errors

### Features
- **DTO**: TotalAmount, RedirectUrl, Buyer, Contact, Address, Item, CreateCheckout, CheckoutResponse, CheckoutDetails, Refund, RefundResponse, WebhookPayload
- **Requests**: CreateCheckoutRequest, GetCheckoutRequest, RefundPaymentRequest
- **Resources**: CheckoutResource with high-level API methods
- **Exceptions**: MayaException, AuthenticationException, ValidationException, WebhookException
- **Laravel Integration**: Service provider, facade, webhook routes, events
- **Testing**: Unit tests for DTO and webhook validation, feature tests with mocked responses

[Unreleased]: https://github.com/szyfr/maya-php-sdk-laravel/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/szyfr/maya-php-sdk-laravel/releases/tag/v1.0.0
