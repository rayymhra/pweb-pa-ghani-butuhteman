<?php
session_start();
require_once '../auth/config.php';

header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID komunitas tidak valid']);
    exit;
}

$community_id = intval($_GET['id']);

// Get community data
function getCommunityById($conn, $community_id) {
    $query = "SELECT c.*, u.name as creator_name, 
              COUNT(cm.id) as member_count
              FROM communities c 
              LEFT JOIN users u ON c.created_by = u.id 
              LEFT JOIN community_memberships cm ON c.id = cm.community_id 
              WHERE c.id = ? 
              GROUP BY c.id";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $community_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $community = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $community;
    }
    return null;
}

// Get community members
function getCommunityMembers($conn, $community_id) {
    $members_query = "SELECT u.id, u.name, u.profile_photo, u.hobby, u.bio, cm.role, cm.joined_at 
                     FROM community_memberships cm 
                     JOIN users u ON cm.user_id = u.id 
                     WHERE cm.community_id = ? 
                     ORDER BY 
                         CASE cm.role 
                             WHEN 'owner' THEN 1 
                             WHEN 'admin' THEN 2 
                             ELSE 3 
                         END,
                         cm.joined_at ASC 
                     LIMIT 8";
    $members = [];
    
    if ($stmt = mysqli_prepare($conn, $members_query)) {
        mysqli_stmt_bind_param($stmt, "i", $community_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $members = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }
    
    return $members;
}

$community = getCommunityById($conn, $community_id);
if (!$community) {
    echo json_encode(['success' => false, 'message' => 'Komunitas tidak ditemukan']);
    exit;
}

// Get community members
$members = getCommunityMembers($conn, $community_id);

// Check if current user is member
$is_member = 0;
if (isset($_SESSION['user'])) {
    $current_user_id = $_SESSION['user']['id'];
    $check_membership = "SELECT id FROM community_memberships WHERE community_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($conn, $check_membership)) {
        mysqli_stmt_bind_param($stmt, "ii", $community_id, $current_user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $is_member = mysqli_num_rows($result);
        mysqli_stmt_close($stmt);
    }
}

$community['is_member'] = $is_member;

// Ensure proper JSON encoding
echo json_encode([
    'success' => true,
    'community' => $community,
    'members' => $members
], JSON_UNESCAPED_UNICODE);

exit;
?>