<?php
require_once 'vendor/autoload.php';
require_once 'MercadoPagoProcessor.php';
require_once 'PaypalProcessor.php';
require_once 'StripeProcessor.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['paymentMethod'];
    $userData = [
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'email' => $_POST['email'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'country' => $_POST['country'],
        'zipCode' => $_POST['zipCode']
    ];

    $orderDetails = [
        'product' => 'Demo Product',
        'price' => 99.99,
        'tax' => 10.00,
        'total' => 109.99
    ];

    try {
        switch ($paymentMethod) {
            case 'MercadoPago':
                $processor = new MercadoPagoProcessor();
                $preferenceUrl = $processor->createPreference($userData, $orderDetails);
                header("Location: $preferenceUrl");
                exit;

            case 'PayPal':
                $processor = new PaypalProcessor();
                $approvalUrl = $processor->createOrder($userData, $orderDetails);
                header("Location: $approvalUrl");
                exit;

            case 'Stripe':
                $processor = new StripeProcessor();
                $checkoutUrl = $processor->createCheckoutSession($userData, $orderDetails);
                header("Location: $checkoutUrl");
                exit;
            default:
                throw new Exception("Invalid payment method selected.");
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h1 class="text-center">Checkout</h1>
            </div>
            <div class="card-body">
                <!-- Personal Information -->
                <form id="checkout-form" action="index.php" method="POST">
                    <div class="mb-4">
                        <h3>Personal Information</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" id="firstName" name="firstName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" id="lastName" name="lastName" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="mb-4">
                        <h3>Billing Address</h3>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" id="country" name="country" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label for="zipCode" class="form-label">ZIP Code</label>
                                <input type="text" id="zipCode" name="zipCode" class="form-control" required>
                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col">
                            <!-- Payment Method -->
                            <div class="mb-4">
                                <h3>Payment Method</h3>
                                <div class="form-check">
                                    <input type="radio" id="mercadoPago" name="paymentMethod" value="MercadoPago" class="form-check-input" required>
                                    <label for="mercadoPago" class="form-check-label">Mercado Pago</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" id="paypal" name="paymentMethod" value="PayPal" class="form-check-input">
                                    <label for="paypal" class="form-check-label">PayPal</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" id="stripe" name="paymentMethod" value="Stripe" class="form-check-input">
                                    <label for="stripe" class="form-check-label">Stripe</label>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <!-- Order Summary -->
                            <div class="mb-4">
                                <h3>Order Summary</h3>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Product: Demo Product
                                        <span>$99.99</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tax
                                        <span>$10.00</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong>Total</strong>
                                        <strong>$109.99</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>