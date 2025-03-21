<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];

    //Update booking status to 'Cancelled'
    $query = "UPDATE bookings SET status = 'Cancelled', payment_status = 'Pending' WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        echo "Booking successfully cancelled.";
    } else {
        echo "Error cancelling booking: " . $stmt->error;
    }
}
?>