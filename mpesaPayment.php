<?php
include 'db.php';
include 'mpesa.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];

    $callbackUrl = "http://localhost/hotelbookingsystem/api/paymentCallback.php";
    $mpesa = new Mpesa();
    $response = $mpesa->stkPush($phone, $amount, $callbackUrl);

    if (isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
        // Save M-PESA request details to the database
        $query ="UPDATE bookings SET  payment_status = 'Pending' WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Payment request sent successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => $response['errorMessage'] ?? "Payment request failed."]);
    }
}
?>