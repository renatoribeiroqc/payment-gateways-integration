<?php
class MercadoPagoProcessor
{
    private $accessToken;

    public function __construct()
    {
        // Load access token from environment variables
        $this->accessToken = $_ENV['MP_ACCESS_TOKEN'];

        if (!$this->accessToken) {
            throw new \Exception('Mercado Pago access token not found in environment variables.');
        }

        // Set SDK access token
        MercadoPago\SDK::setAccessToken($this->accessToken);
    }

    /**
     * Create a payment preference for Mercado Pago checkout.
     *
     * @param array $userData User details (name, email, address).
     * @param array $orderDetails Order details (product, price, tax, total).
     * @return string URL to redirect the user to Mercado Pago payment page.
     * @throws \Exception if preference creation fails.
     */
    public function createPreference(array $userData, array $orderDetails): string
    {
        try {
            $preference = new MercadoPago\Preference();

            // Add item details
            $item = new MercadoPago\Item();
            $item->title = $orderDetails['product'];
            $item->quantity = 1;
            $item->unit_price = $orderDetails['total'];
            $item->currency_id = 'BRL'; // Set your currency (e.g., USD, BRL).
            $preference->items = [$item];

            // Add payer details
            $payer = new MercadoPago\Payer();
            $payer->name = $userData['firstName'];
            $payer->surname = $userData['lastName'];
            $payer->email = $userData['email'];
            $payer->address = [
                'street_name' => $userData['address'],
                'zip_code' => $userData['zipCode']
            ];
            $preference->payer = $payer;

            // Configure return URLs
            $preference->back_urls = [
                "success" => $_ENV['HTTP_SERVER_URL'] . "index.php?m=success",
                "failure" => $_ENV['HTTP_SERVER_URL'] . "index.php?m=failure",
                "pending" => $_ENV['HTTP_SERVER_URL'] . "index.php?m=pending"
            ];
            $preference->auto_return = "approved";

            // Notification URL
            $preference->notification_url = getenv('MP_WEBHOOK_URL') ?? '';

            // Save preference and get the checkout URL
            $preference->save();
            return $preference->init_point;
        } catch (\Exception $e) {
            throw new \Exception("Error creating Mercado Pago preference: " . $e->getMessage());
        }
    }
}
