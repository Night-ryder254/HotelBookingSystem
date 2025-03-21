<?php
include 'db.php';
session_start();

$bookingId = $_GET['booking_id'];
$customerId = $_SESSION['customer_id'];

$stmt = $conn->prepare("UPDATE bookings SET status = 'Checked-Out' WHERE booking_id = ? AND customer_id = ?");
$stmt->bind_param("ii", $bookingId, $customerId);

if ($stmt->execute()) {
    echo "Checked out successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>