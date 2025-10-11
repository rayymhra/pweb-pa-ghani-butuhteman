<?php
// auto_reject_bookings.php
// This script should be run periodically via cron job

require_once 'config.php';

// Auto-reject bookings that are still pending and the start time is less than 24 hours away
$auto_reject_query = "UPDATE bookings 
                      SET status = 'rejected' 
                      WHERE status = 'pending' 
                      AND start_datetime <= DATE_ADD(NOW(), INTERVAL 24 HOUR)";

if (mysqli_query($conn, $auto_reject_query)) {
    $affected_rows = mysqli_affected_rows($conn);
    if ($affected_rows > 0) {
        error_log("Auto-rejected $affected_rows pending bookings");
    }
}

// Auto-complete bookings that have passed their end time
$auto_complete_query = "UPDATE bookings 
                       SET status = 'completed' 
                       WHERE status = 'accepted' 
                       AND end_datetime <= NOW()";

if (mysqli_query($conn, $auto_complete_query)) {
    $affected_rows = mysqli_affected_rows($conn);
    if ($affected_rows > 0) {
        error_log("Auto-completed $affected_rows accepted bookings");
    }
}
?>