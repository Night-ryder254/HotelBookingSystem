<?php
include 'db.php';

if (isset($_GET['check_in']) && isset($_GET['check_out'])) {
$checkIn = $_GET['check_in'];
$checkOut = $_GET['check_out'];

$query = " SELECT r.room_id, r.room_number, rt.name AS room_type, rt.price_per_night
           FROM rooms r
           JOIN room_types rt ON r.room_type_id = rt.room_type_id
           WHERE r.room_id NOT IN (
           SELECT room_id
           FROM bookings
           WHERE NOT (check_out_date <= ? OR check_in_date >= ?)
           ) AND r.status = 'available'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $checkIn, $checkOut);
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $rows;
}

echo json_encode($rooms);

$stmt->close();
$conn->close();
}
?>