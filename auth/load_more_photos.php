<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id']) || !isset($_GET['page'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = intval($_GET['user_id']);
$page = intval($_GET['page']);
$limit = 6;
$offset = ($page - 1) * $limit;

// Get photos for the specific page
$query = "SELECT * FROM gallery_photos WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$photos = [];

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $photos = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Format dates and check if there are more photos
$formatted_photos = [];
foreach ($photos as $photo) {
    $formatted_photos[] = [
        'id' => $photo['id'],
        'photo_path' => $photo['photo_path'],
        'created_at_formatted' => date('d M Y', strtotime($photo['created_at']))
    ];
}

// Check if there are more photos
$has_more = count($photos) === $limit;

echo json_encode([
    'success' => true,
    'photos' => $formatted_photos,
    'hasMore' => $has_more
]);
?>