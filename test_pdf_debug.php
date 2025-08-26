<?php
// Simple debug test for PDF generation issue
// This can be accessed directly to test data flow

require_once 'vendor/autoload.php';

echo "<h1>PDF Generation Debug Test</h1>";

// Test 1: Check if the data is being passed correctly
echo "<h2>Test 1: Request Data Check</h2>";
echo "<pre>";
echo "POST Data:\n";
print_r($_POST);
echo "\nGET Data:\n";
print_r($_GET);
echo "</pre>";

// Test 2: Check if Laravel session has data
echo "<h2>Test 2: Check Laravel Environment</h2>";
if (file_exists('bootstrap/app.php')) {
    echo "✓ Laravel bootstrap file exists\n";
} else {
    echo "✗ Laravel bootstrap file missing\n";
}

if (file_exists('resources/views/pages/support_team/payments/payment_notifications_pdf.blade.php')) {
    echo "✓ PDF template exists\n";
} else {
    echo "✗ PDF template missing\n";
}

// Test 3: Simple form to test the data transmission
echo "<h2>Test 3: Manual Test Form</h2>";
echo '<form method="POST" action="/payments/generate_notifications">
    <input type="hidden" name="_token" value="' . (function_exists('csrf_token') ? csrf_token() : 'test-token') . '">
    <input type="text" name="my_class_id" value="1" placeholder="Class ID">
    <input type="text" name="my_payments_id[]" value="1" placeholder="Payment ID">
    <input type="text" name="payment_deadline" value="' . date('Y-m-d', strtotime('+1 month')) . '">
    <input type="hidden" name="action" value="download">
    <input type="text" name="status[]" value="Normal">
    <button type="submit">Test PDF Generation</button>
</form>';

echo "<h2>Debug Instructions</h2>";
echo "<p>1. Check the Laravel logs at storage/logs/laravel.log for detailed error messages</p>";
echo "<p>2. Verify that all form data is being transmitted correctly</p>";
echo "<p>3. Make sure the PaymentController validation is not rejecting the request</p>";
echo "<p>4. Check that unpaid students are found and processed</p>";

?>