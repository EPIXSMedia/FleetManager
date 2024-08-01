<?php
include('config.php');

$sql = "SELECT bookings.id, vehicles.make, vehicles.model, bookings.customer_name, bookings.start_date, bookings.end_date 
        FROM bookings 
        JOIN vehicles ON bookings.vehicle_id = vehicles.id";
$result = mysqli_query($conn, $sql);

$events = [];

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'title' => $row['customer_name'] . ' - ' . $row['make'] . ' ' . $row['model'],
        'start' => $row['start_date'],
        'end' => $row['end_date']
    ];
}

echo json_encode($events);
?>