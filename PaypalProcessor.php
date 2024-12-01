<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PaypalProcessor
{
    private PayPalHttpClient $client;

    public function __construct()
    {
        // Get PayPal credentials from environment variables
        $clientId = $_ENV['PAYPAL_CLIENT_ID'];
        $clientSecret = $_ENV['PAYPAL_CLIENT_SECRET'];
        $mode = isset($_ENV['PAYPAL_MODE']) ? $_ENV['PAYPAL_MODE'] : 'sandbox';

        if (!$clientId || !$clientSecret) {
            throw new \Exception('PayPal credentials not found in environment variables.');
        }

        // Initialize the PayPal environment
        $environment = $mode === 'sandbox'
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

    /**
     * Create a PayPal order and return the approval URL.
     *
     * @param array $userData User information (name, email, etc.).
     * @param array $orderDetails Order information (product name, amount, etc.).
     * @return string Approval URL for PayPal checkout.
     * @throws \Exception If order creation fails.
     */
    public function createOrder(array $userData, array $orderDetails): string
    {
        try {
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');

            $request->body = [
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => $_ENV['HTTP_SERVER_URL'] . "index.php?m=success",
                    'cancel_url' => $_ENV['HTTP_SERVER_URL'] . "index.php?m=cancel",
                    'brand_name' => "PAYMENT DEMO CHECKOUT",
                    'user_action' => 'PAY_NOW',
                ],
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD', // Set currency
                        'value' => number_format($orderDetails['total'], 2, '.', '')
                    ],
                    'description' => $orderDetails['product']
                ]]
            ];

            $response = $this->client->execute($request);

            if ($response->statusCode !== 201) {
                throw new \Exception('Failed to create PayPal order.');
            }

            // Extract the approval URL
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    return $link->href;
                }
            }

            throw new \Exception('Approval URL not found in PayPal response.');
        } catch (\Exception $e) {
            throw new \Exception('PayPal order creation failed: ' . $e->getMessage());
        }
    }
}
