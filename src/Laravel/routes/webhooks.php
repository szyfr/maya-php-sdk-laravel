<?php

use Illuminate\Support\Facades\Route;
use Szyfr\Maya\Laravel\Http\Controllers\WebhookController;

$route = Route::post(
    config('maya.webhook.route_path', 'webhooks/maya'),
    [WebhookController::class, 'handle']
);
// @phpstan-ignore-next-line - Route::post() returns Route instance
$route->middleware(config('maya.webhook.middleware', ['api']))->name('maya.webhook');
