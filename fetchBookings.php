<?php
include 'db.php';
session_start();

if (isset($_SESSION['customer_id'])) {
    echo "Please log in first.";
    exit();
}
 $customerId = $_SESSION['customer_id'];
 $query = "SELECT * FROM bookings WHERE customer_id = ?";
 $stmt = $conn->prepare($query);
 $stmt->bind_param("i", $customerId);
 $stmt->execute();
 $result = $stmt->get_result();

 $bookings = [];
 while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
 }

 echo json_encode($bookings);
 ?>