<?php
include 'db.php';

$data = file_get_contents('php://input');
$response = json_decode($data, true);

if (isset($response['Body']['stkCallback'])) {
    $callback = $response['Body']['stkCallback'];

    $mpesaCode = $callback['CallbackMetadata']['Item'][1]['Value'] ?? null;
    $bookingId = $_POST['booking_id'];

    if ($callback['ResultCode'] == 0) {
        // Update booking payment status
        $query = "UPDATE bookings SET payment_status = 'Paid', mpesa_reference = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $mpesaCosde, $bookingId);
        $stmt->execute();
    } else {
        // Handle failed payment
        $query = "UPDATE bookings SET payment_status = 'Failed' WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
    }
}
?>