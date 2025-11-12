<?php
/**
 * Process Checkout Action
 * Handles the complete checkout workflow after payment confirmation
 *
 * Process:
 * 1. Validate user is logged in
 * 2. Get cart items
 * 3. Generate unique order reference and invoice number
 * 4. Create order in orders table
 * 5. Add all cart items to orderdetails table
 * 6. Record payment in payment table
 * 7. Empty the cart
 * 8. Return success with order details
 */

session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');
require_once(dirname(__FILE__).'/../controllers/order_controller.php');
require_once(dirname(__FILE__).'/../settings/core.php');

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

// Verify user is logged in (guests cannot checkout)
$customer_id = getUserID();
if (!$customer_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to complete checkout. Please login or register.'
    ]);
    exit;
}

// Get IP address (for cart identification)
$ip_address = $_SERVER['REMOTE_ADDR'];

// Get cart items
$cart_items = get_user_cart_ctr($customer_id, $ip_address);

// Validate cart is not empty
if (empty($cart_items)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Your cart is empty. Add some products before checking out.'
    ]);
    exit;
}

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += ($item['product_price'] * $item['qty']);
}

// Get currency from POST (default to USD)
$currency = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';

// Generate unique identifiers
$order_reference = generate_order_reference_ctr();
$invoice_no = generate_invoice_number_ctr();
$order_date = date('Y-m-d');
$payment_date = date('Y-m-d');

// Start transaction (to ensure data consistency)
// If any step fails, we can rollback

try {
    // Step 1: Create order
    $order_id = create_order_ctr($customer_id, $invoice_no, $order_date, 'pending');

    if (!$order_id) {
        throw new Exception('Failed to create order.');
    }

    // Step 2: Add order details for each cart item
    foreach ($cart_items as $item) {
        $detail_result = add_order_details_ctr(
            $order_id,
            $item['p_id'],
            $item['qty']
        );

        if (!$detail_result) {
            throw new Exception('Failed to add order details for product: ' . $item['product_title']);
        }
    }

    // Step 3: Record payment
    $payment_id = record_payment_ctr(
        $total_amount,
        $customer_id,
        $order_id,
        $currency,
        $payment_date
    );

    if (!$payment_id) {
        throw new Exception('Failed to record payment.');
    }

    // Step 4: Update order status to completed
    $status_updated = update_order_status_ctr($order_id, 'completed');

    // Step 5: Empty the cart
    $cart_emptied = empty_cart_ctr($customer_id, $ip_address);

    if (!$cart_emptied) {
        // Log warning but don't fail the transaction
        // Cart can be manually cleared later
        error_log("Warning: Failed to empty cart for customer $customer_id after successful checkout");
    }

    // Success! Return order details
    echo json_encode([
        'status' => 'success',
        'message' => 'Order placed successfully!',
        'data' => [
            'order_id' => $order_id,
            'order_reference' => $order_reference,
            'invoice_no' => $invoice_no,
            'total_amount' => number_format($total_amount, 2),
            'currency' => $currency,
            'order_date' => $order_date,
            'payment_id' => $payment_id,
            'items_count' => count($cart_items)
        ]
    ]);

} catch (Exception $e) {
    // Transaction failed - return error
    echo json_encode([
        'status' => 'error',
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
    exit;
}
?>
