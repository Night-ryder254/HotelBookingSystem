<?php
include 'db.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$customerId = $_SESSION['customer_id'];
$roomId = $_POST['room_id'];
$checkIn = $_POST['check_in'];
$checkOut = $_POST['check_out'];

$query = "SELECT price FROM rooms where room_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $room = $result->fetch_assoc();
    $days = (strtotime($checkOut) - strtotime($checkIn)) / 86400;
    $totalPrice = $room['price'] * $days;

    $bookingQuery = " INSERT INTO bookings (customer_id, room_id, check_in_date, check_out_date, total_price)
                      VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($bookingQuery);
    $stmt->bind_param("iissd", $customerId, $roomId, $checkIn, $checkOut, $totalPrice);

    if ($stmt->execute()) {
        echo "Room booked successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
}
?>