# Checkout Integration Demo

Welcome to the **Checkout Integration Demo**, a fully functional project showcasing integration with three popular payment gateways: **Mercado Pago**, **PayPal**, and **Stripe**. This project is designed as a demo application for dynamic payment processing using PHP and is part of my professional portfolio.

You can check a [Working Demo here](https://conectaexperience.com.br/payment-gateways-integration)
---

## Features

- **Multiple Payment Gateways**:
  - **Mercado Pago**: Integration for payments with itemized preferences and user details.
  - **PayPal**: Secure and flexible payment processing with order capture and refund capabilities.
  - **Stripe**: Checkout session management and payment intent handling.
- **Dynamic User Interface**:
  - Responsive checkout form using **Bootstrap** for modern design and usability.
  - User-friendly interface to input personal, billing, and payment details.
- **Secure Environment**:
  - Credentials managed securely through environment variables using `vlucas/phpdotenv`.
- **Real-World Scenarios**:
  - Supports success, failure, and pending states for payments.
  - Easy-to-configure webhook handlers for real-time updates.

---

## Installation

### Prerequisites

1. **PHP 8.0+** with `composer` installed.
2. Valid API credentials for:
   - **Mercado Pago**
   - **PayPal**
   - **Stripe**
3. A server environment (local or live) with HTTPS enabled.

---

### Steps to Install

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/checkout-integration-demo.git
   cd checkout-integration-demo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment variables:
   - Copy the `.env.example` to `.env`.
   - Update the `.env` file with your API credentials and server details:
     ```env
     MP_ACCESS_TOKEN=your-mercado-pago-access-token
     PAYPAL_CLIENT_ID=your-paypal-client-id
     PAYPAL_CLIENT_SECRET=your-paypal-client-secret
     STRIPE_SECRET_KEY=your-stripe-secret-key
     HTTP_SERVER_URL=https://your-domain.com/
     ```

4. Run the project on a local server:
   ```bash
   php -S localhost:8000
   ```

5. Open the app in your browser:
   ```
   http://localhost:8000
   ```

---

## Usage

1. Fill in personal and billing details on the checkout form.
2. Select a payment method:
   - **Mercado Pago**: Redirects to Mercado Pago's hosted checkout page.
   - **PayPal**: Redirects to PayPal for approval.
   - **Stripe**: Redirects to Stripe's hosted checkout page.
3. After payment, you are redirected back to the app to see the success, failure, or pending status.

---

## Project Structure

- **index.php**:
  - Main entry point for the application.
  - Manages form submission and routes requests to the appropriate payment gateway.

- **MercadoPagoProcessor.php**:
  - Handles Mercado Pago integration.
  - Creates preferences with itemized details and redirect URLs.

- **PaypalProcessor.php**:
  - Handles PayPal integration.
  - Manages order creation and payment capture.

- **StripeProcessor.php**:
  - Handles Stripe integration.
  - Creates checkout sessions and manages payment intents.

- **composer.json**:
  - Dependency management with packages like `vlucas/phpdotenv`, `mercadopago/dx-php`, `paypal/paypal-server-sdk`, and `stripe/stripe-php`.

---

## Key Code Highlights

### Payment Gateway Selection (`index.php`)
```php
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
```

---

## Dependencies

- **PHP Libraries**:
  - [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) for environment variable management.
  - [mercadopago/dx-php](https://github.com/mercadopago/dx-php) for Mercado Pago integration.
  - [paypal/paypal-server-sdk](https://github.com/paypal/PayPal-PHP-SDK) for PayPal integration.
  - [stripe/stripe-php](https://github.com/stripe/stripe-php) for Stripe integration.

---

## License

This project is licensed under the MIT License. Feel free to fork, modify, and use it in your projects.

---

## Contact

Visit my [GitHub Profile](https://github.com/renatoribeiroqc) or my [Upwork Profile](https://www.upwork.com/freelancers/renatoribeiro).