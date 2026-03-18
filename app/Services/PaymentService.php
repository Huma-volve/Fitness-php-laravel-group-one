<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentService
{
    // ─── URL Generation ───────────────────────────────────────────────────────────
    public function generatePaypalUrl(float $amount, int $bookingId): array
    {
        $baseUrl = $this->paypalBaseUrl();
        $token   = $this->paypalAccessToken($baseUrl);

        $response = Http::withToken($token)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent'         => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => (string) $bookingId,
                    'description'  => 'Training Session Booking #' . $bookingId,
                    'amount'       => [
                        'currency_code' => 'USD',
                        'value'         => number_format($amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => 'http://127.0.0.1:8000/payment/success',
//                    'return_url'          => config('app.frontend_url') . '/booking/success',
//                    'cancel_url'          => config('app.frontend_url') . '/booking/cancel?booking_id=' . $bookingId,
                    'user_action'         => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ])
            ->json();

        // Extract the approve link from PayPal response links
        $approveUrl = collect($response['links'])
            ->firstWhere('rel', 'approve')['href'];

        return [
            'url'       => $approveUrl,      // Redirect trainee here
            'reference' => $response['id'],   // PayPal order ID — stored as gateway_reference
        ];
    }

    // ─── Verification ─────────────────────────────────────────────────────────────

    public function verifyPaypal(string $orderId): array
    {
        $baseUrl = $this->paypalBaseUrl();
        $token   = $this->paypalAccessToken($baseUrl);

        // Attempt to capture the order.
        // PayPal requires Content-Type: application/json AND an explicit empty body {}.
        // Sending no body causes: "Request is not well-formed, syntactically incorrect"
        $captureResponse = Http::withToken($token)
            ->withHeaders([
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json',
                'Prefer'            => 'return=representation',
                'PayPal-Request-Id' => 'booking-' . $orderId, // Idempotency key
            ])
            ->withBody('{}', 'application/json')
            ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

        $response = $captureResponse->json();

        // If PayPal returns an error, inspect it
        if ($captureResponse->failed() || isset($response['name'])) {
            $errorName = $response['name'] ?? 'UNKNOWN_ERROR';

            // ORDER_ALREADY_CAPTURED means it was already paid — fetch the order instead
            if ($errorName === 'ORDER_ALREADY_CAPTURED') {
                $fetchResponse = Http::withToken($token)
                    ->get("{$baseUrl}/v2/checkout/orders/{$orderId}")
                    ->json();

                $response = $fetchResponse;
            } else {
                // Any other PayPal error — payment failed or was declined
                throw new \RuntimeException(
                    'PayPal error: ' . ($response['message'] ?? $errorName)
                );
            }
        }

        // Safely extract status and capture details
        $status  = $response['status'] ?? null;
        $capture = $response['purchase_units'][0]['payments']['captures'][0] ?? null;

        return [
            'verified'       => $status === 'COMPLETED',
            'reference'      => $orderId,
            'transaction_id' => $capture['id'] ?? null,
            'amount'         => (float) ($capture['amount']['value'] ?? 0),
        ];
    }

    //  Refund

    public function refund(string $gatewayReference): void
    {
        $this->paypalRefund($gatewayReference);
    }


    private function paypalRefund(string $captureId): void
    {
        $baseUrl = $this->paypalBaseUrl();

        Http::withToken($this->paypalAccessToken($baseUrl))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])
            ->withBody('{}', 'application/json')
            ->post("{$baseUrl}/v2/payments/captures/{$captureId}/refund");
    }

    // Helpers

    private function paypalBaseUrl(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function paypalAccessToken(string $baseUrl): string
    {
        return Http::withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret')
        )->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials'])
            ->json('access_token');
    }
}
