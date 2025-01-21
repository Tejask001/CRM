<?php
require "../../config.php";

if (isset($_GET['order_id'])) {
    $order_id = $conn->real_escape_string($_GET['order_id']);

    $sql = "SELECT 
                vehicle_no,
                driver_name,
                driver_phone,
                driver_gst_no,	
                estimated_delivery_date,	
                is_transferred,	
                client_vehicle_no,	
                client_driver_name,	
                client_driver_phone,	
                client_driver_gst_no,	
                transfer_date
            FROM logistics
            WHERE order_id = '$order_id'";

    if ($result = $conn->query($sql)) {
        if ($orderDetails = $result->fetch_assoc()) {
            echo json_encode($orderDetails);
        } else {
            echo json_encode(['error' => 'Order details not found.']);
        }
    } else {
        echo json_encode(['error' => 'Database query failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'No order ID provided.']);
}
