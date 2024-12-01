<?php
class StripeProcessor
{
    private \Stripe\StripeClient $stripeClient;

    public function __construct()
    {
        $apiKey = $_ENV['STRIPE_SECRET_KEY'];
        if (!$apiKey) {
            throw new \Exception('Stripe API key not found in environment variables');
        }

        $this->stripeClient = new \Stripe\StripeClient($apiKey);
    }

    /**
     * Create a Stripe Checkout Session.
     *
     * @param array $userData User details
     * @param array $orderDetails Order details
     * @return string URL to Stripe Checkout
     * @throws \Exception If Checkout Session creation fails
     */
    public function createCheckoutSession(array $userData, array $orderDetails): string
    {
        try {
            $checkoutSession = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $orderDetails['product'],
                        ],
                        'unit_amount' => $orderDetails['total'] * 100, // Stripe requires the amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $_ENV['HTTP_SERVER_URL'] . 'index.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $_ENV['HTTP_SERVER_URL'] . 'index.php?m=cancel',
            ]);

            return $checkoutSession->url;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe Checkout session: ' . $e->getMessage());
        }
    }
}
