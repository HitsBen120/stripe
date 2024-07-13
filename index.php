<?php
// Include the Stripe PHP library
require_once('vendor/autoload.php');

// Set your Stripe API key
\Stripe\Stripe::setApiKey('YOUR_STRIPE_API_KEY');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $cardName = $_POST['cardName'];
    $cardNumber = $_POST['cardNumber'];
    $cardExpiry = $_POST['cardExpiry'];
    $cardCvc = $_POST['cardCvc'];
    $cardAmount = $_POST['cardAmount'];

    try {
        // Create a new Stripe token
        $token = \Stripe\Token::create([
            'card' => [
                'name' => $cardName,
                'number' => $cardNumber,
                'exp_month' => explode('/', $cardExpiry)[0],
                'exp_year' => '20' . explode('/', $cardExpiry)[1],
                'cvc' => $cardCvc
            ]
        ]);

        // Charge the card
        $charge = \Stripe\Charge::create([
            'amount' => $cardAmount * 100, // Amount in cents
            'currency' => 'usd',
            'source' => $token->id,
            'description' => 'Charge for ' . $cardName
        ]);

        // Charge successful
        echo '<h2>Charge Successful</h2>';
        echo '<p>Charge ID: ' . $charge->id . '</p>';
        echo '<p>Amount: $' . $charge->amount / 100 . '</p>';
    } catch (\Stripe\Error\Card $e) {
        // Card error
        echo '<h2>Card Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (\Stripe\Error\RateLimit $e) {
        // Too many requests made to the API too quickly
        echo '<h2>Rate Limit Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (\Stripe\Error\InvalidRequest $e) {
        // Invalid parameters were supplied to Stripe's API
        echo '<h2>Invalid Request Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (\Stripe\Error\Authentication $e) {
        // Authentication with Stripe's API failed
        echo '<h2>Authentication Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (\Stripe\Error\ApiConnection $e) {
        // Network communication with Stripe failed
        echo '<h2>API Connection Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (\Stripe\Error\Base $e) {
        // Display a very generic error to the user
        echo '<h2>Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    } catch (Exception $e) {
        // Any other exception
        echo '<h2>Error</h2>';
        echo '<p>' . $e->getMessage() . '</p>';
    }
}
?>