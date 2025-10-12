<?php
session_start();
require_once '../auth/config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_session = $_SESSION['user'];
$current_user_id = $user_session['id'];
$current_user_name = $user_session['name'];
$current_user_photo = !empty($user_session['profile_photo']) ? $user_session['profile_photo'] : '../assets/img/default-user.png';

// Get community ID from URL
if (!isset($_GET['community_id'])) {
    die("Community ID tidak valid");
}

$community_id = intval($_GET['community_id']);

// Get community data
$community_query = "SELECT c.*, u.name as creator_name, 
                   COUNT(cm.id) as member_count
                   FROM communities c 
                   LEFT JOIN users u ON c.created_by = u.id 
                   LEFT JOIN community_memberships cm ON c.id = cm.community_id 
                   WHERE c.id = ? 
                   GROUP BY c.id";

$community = null;
if ($stmt = mysqli_prepare($conn, $community_query)) {
    mysqli_stmt_bind_param($stmt, "i", $community_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $community = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$community) {
    die("Komunitas tidak ditemukan");
}

// Check if user is a member of this community
$is_member_query = "SELECT id, role FROM community_memberships WHERE community_id = ? AND user_id = ?";
$is_member = false;
$user_role = 'non-member';

if ($stmt = mysqli_prepare($conn, $is_member_query)) {
    mysqli_stmt_bind_param($stmt, "ii", $community_id, $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($membership = mysqli_fetch_assoc($result)) {
        $is_member = true;
        $user_role = $membership['role'];
    }
    mysqli_stmt_close($stmt);
}

// Handle join community
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_community'])) {
    $join_query = "INSERT INTO community_memberships (community_id, user_id, role) VALUES (?, ?, 'member')";
    if ($stmt = mysqli_prepare($conn, $join_query)) {
        mysqli_stmt_bind_param($stmt, "ii", $community_id, $current_user_id);
        mysqli_stmt_execute($stmt);
        $is_member = true;
        $user_role = 'member';
        $_SESSION['join_success'] = "Berhasil bergabung dengan komunitas!";
        header("Location: community_chat.php?community_id=" . $community_id);
        exit();
    }
}

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message']) && $is_member) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insert_message = "INSERT INTO community_messages (community_id, user_id, message) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $insert_message)) {
            mysqli_stmt_bind_param($stmt, "iis", $community_id, $current_user_id, $message);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // REDIRECT AFTER POST to prevent resubmission
            header("Location: community_chat.php?community_id=" . $community_id);
            exit();
        }
    }
}

// Get community members
$members_query = "SELECT u.id, u.name, u.profile_photo, cm.role 
                 FROM community_memberships cm 
                 JOIN users u ON cm.user_id = u.id 
                 WHERE cm.community_id = ? 
                 ORDER BY 
                     CASE cm.role 
                         WHEN 'owner' THEN 1 
                         WHEN 'admin' THEN 2 
                         ELSE 3 
                     END,
                     cm.joined_at ASC";

$members = [];
if ($stmt = mysqli_prepare($conn, $members_query)) {
    mysqli_stmt_bind_param($stmt, "i", $community_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $members = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Get messages (everyone can see messages, even non-members)
$messages_query = "SELECT cm.*, u.name as user_name, u.profile_photo 
                  FROM community_messages cm 
                  JOIN users u ON cm.user_id = u.id 
                  WHERE cm.community_id = ? 
                  ORDER BY cm.created_at ASC 
                  LIMIT 100";

$messages = [];
if ($stmt = mysqli_prepare($conn, $messages_query)) {
    mysqli_stmt_bind_param($stmt, "i", $community_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Determine the previous page for the back button
$previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../komunitas/index.php';
// Make sure we don't redirect to the same page
if (strpos($previous_page, 'community_chat.php') !== false) {
    $previous_page = '../komunitas/index.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($community['name']); ?> - Butuh Teman</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap');

        * {
            font-family: "Josefin Sans", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #315DB4;
            --primary-dark: #06206C;
            --accent-color: #FECE6A;
            --light-bg: #f0f2f5;
            --dark-text: #333;
            --light-text: #fff;
            --whatsapp-green: #25D366;
            --whatsapp-chat-bg: #e5ddd5;
            --whatsapp-header: #f0f0f0;
        }
        
        body {
            background-color: var(--light-bg);
            color: var(--dark-text);
            height: 100vh;
            overflow: hidden;
        }
        
        /* WhatsApp-like Chat Container */
        .whatsapp-container {
            display: flex;
            height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: 30%;
            background-color: white;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            min-width: 300px;
        }
        
        .sidebar-header {
            background-color: var(--whatsapp-header);
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-header h5 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .group-info-sidebar {
            padding: 20px 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .group-avatar-sidebar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid var(--accent-color);
        }
        
        .group-details-sidebar h6 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-dark);
        }
        
        .group-details-sidebar p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        
        .search-container {
            margin-bottom: 20px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 0.9rem;
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .sidebar-section {
            margin-bottom: 25px;
        }
        
        .sidebar-section h6 {
            font-size: 1rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .description-text {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #555;
            background: #f9f9f9;
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid var(--accent-color);
        }
        
        .member-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.2s;
        }
        
        .member-item:hover {
            background: #f9f9f9;
            border-radius: 5px;
            padding-left: 5px;
            padding-right: 5px;
        }
        
        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary-color), #4a7ad9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.1rem;
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .member-name {
            flex: 1;
            font-weight: 500;
        }
        
        .member-role {
            font-size: 0.8rem;
            color: #666;
            font-style: italic;
            padding: 2px 8px;
            border-radius: 10px;
            background: #f0f0f0;
        }
        
        .role-owner {
            background: #ffd700;
            color: #000;
        }
        
        .role-admin {
            background: #4CAF50;
            color: white;
        }
        
        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--whatsapp-chat-bg);
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            position: relative;
        }
        
        .chat-header {
            background-color: var(--whatsapp-header);
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .chat-header-info {
            display: flex;
            align-items: center;
        }
        
        .chat-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
            border: 2px solid var(--accent-color);
        }
        
        .chat-title h6 {
            margin: 0;
            font-weight: 600;
            color: var(--primary-dark);
        }
        
        .chat-title p {
            margin: 0;
            font-size: 0.85rem;
            color: #666;
        }
        
        .chat-actions {
            display: flex;
            gap: 15px;
        }
        
        .chat-actions button {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background 0.2s;
        }
        
        .chat-actions button:hover {
            background: rgba(0,0,0,0.05);
        }
        
        .messages-container {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            max-width: 70%;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 7.5px;
            position: relative;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message.received {
            background-color: white;
            align-self: flex-start;
            border-top-left-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .message.sent {
            background-color: #dcf8c6;
            align-self: flex-end;
            border-top-right-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .message-sender {
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 3px;
            color: var(--primary-color);
        }
        
        .message-content {
            margin: 0;
            line-height: 1.4;
        }
        
        .message-time {
            font-size: 0.7rem;
            color: #666;
            text-align: right;
            margin-top: 3px;
        }
        
        .chat-input-area {
            padding: 10px 15px;
            background-color: var(--whatsapp-header);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chat-input {
            flex: 1;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            outline: none;
            font-size: 0.9rem;
        }
        
        .send-btn {
            background-color: var(--whatsapp-green);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .send-btn:hover {
            background-color: #1da851;
        }
        
        .send-btn:active {
            transform: scale(0.95);
        }
        
        .send-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        /* Join Overlay */
        .join-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            backdrop-filter: blur(3px);
        }
        
        .join-content {
            text-align: center;
            max-width: 80%;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .join-content h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .join-content p {
            margin-bottom: 25px;
            color: #666;
            line-height: 1.5;
        }
        
        .btn-join {
            background-color: var(--whatsapp-green);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-join:hover {
            background-color: #1da851;
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 35%;
                min-width: 280px;
            }
        }
        
        @media (max-width: 768px) {
            .whatsapp-container {
                flex-direction: column;
                height: 100vh;
            }
            
            .sidebar {
                width: 100%;
                height: 40vh;
                min-width: auto;
                display: none;
            }
            
            .chat-area {
                height: 60vh;
            }
            
            .sidebar.active {
                display: flex;
            }
            
            .chat-area.hidden {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .message {
                max-width: 85%;
            }
            
            .sidebar-header h5 {
                font-size: 1rem;
            }
            
            .group-details-sidebar h6 {
                font-size: 1rem;
            }
        }

        /* Back Button Styles */
.back-btn {
    background: none;
    border: none;
    color: var(--primary-color);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

.back-btn:hover {
    background: rgba(0,0,0,0.05);
}

/* Adjust chat header info to accommodate back button */
.chat-header-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.chat-title {
    flex: 1;
}
    </style>
</head>
<body>
    <!-- WhatsApp Container -->
    <div class="whatsapp-container">
        <!-- Sidebar (Profil Grup) -->
        <div class="sidebar active">
            <div class="sidebar-header">
                <h5>Info Grup</h5>
            </div>
            
            <div class="group-info-sidebar">
                <img src="<?php echo !empty($community['photo']) ? '../admin/' . htmlspecialchars($community['photo']) : '../assets/img/kom.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($community['name']); ?>" 
                     class="group-avatar-sidebar">
                <div class="group-details-sidebar">
                    <h6><?php echo htmlspecialchars($community['name']); ?></h6>
                    <p><?php echo count($members); ?> members</p>
                </div>
            </div>
            
            <div class="sidebar-content">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search in conversation" id="searchInput">
                </div>
                
                <div class="sidebar-section">
                    <h6>Deskripsi Grup</h6>
                    <div class="description-text">
                        <?php echo !empty($community['description']) ? htmlspecialchars($community['description']) : 'Bergabunglah dengan komunitas ini untuk berdiskusi dan menemukan teman sefrekuensi!'; ?>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <h6>Anggota Grup (<?php echo count($members); ?>)</h6>
                    <ul class="member-list">
                        <?php foreach ($members as $member): ?>
                            <li class="member-item">
                                <div class="member-avatar">
                                    <?php if (!empty($member['profile_photo'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['profile_photo']); ?>" 
                                             alt="<?php echo htmlspecialchars($member['name']); ?>"
                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<?php echo substr($member['name'], 0, 1); ?>'">
                                    <?php else: ?>
                                        <?php echo substr($member['name'], 0, 1); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="member-name"><?php echo htmlspecialchars($member['name']); ?></div>
                                <div class="member-role <?php echo 'role-' . $member['role']; ?>">
                                    <?php 
                                    if ($member['role'] === 'owner') echo 'Pendiri';
                                    elseif ($member['role'] === 'admin') echo 'Admin';
                                    else echo 'Anggota';
                                    ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="chat-area">
            <!-- Join Overlay (akan hilang setelah join) -->
            <?php if (!$is_member): ?>
            <div class="join-overlay" id="joinOverlay">
                <div class="join-content">
                    <i class="fas fa-lock fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h3>Bergabung untuk chat</h3>
                    <p>Anda perlu bergabung dengan grup ini untuk dapat berpartisipasi dalam percakapan.</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="join_community" value="1">
                        <button type="submit" class="btn-join">
                            <i class="fas fa-sign-in-alt me-2"></i> Bergabung Sekarang
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Chat Header -->
<div class="chat-header">
    <div class="chat-header-info">
        <button class="d-md-none" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Back Button -->
        <button class="back-btn me-2" onclick="goBack()" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </button>
        <img src="<?php echo !empty($community['photo']) ? '../admin/' . htmlspecialchars($community['photo']) : 'https://via.placeholder.com/45/315DB4/FFFFFF?text=GP'; ?>" 
             alt="<?php echo htmlspecialchars($community['name']); ?>" 
             class="chat-avatar"
             onerror="this.src='https://via.placeholder.com/45/315DB4/FFFFFF?text=GP'">
        <div class="chat-title">
            <h6><?php echo htmlspecialchars($community['name']); ?></h6>
            <p><?php echo count($members); ?> members</p>
        </div>
    </div>
    <div class="chat-actions">
        <button id="searchBtn" title="Search">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>
            
            <!-- Messages Container -->
            <div class="messages-container" id="messagesContainer">
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo $message['user_id'] == $current_user_id ? 'sent' : 'received'; ?>">
                        <?php if ($message['user_id'] != $current_user_id): ?>
                            <div class="message-sender"><?php echo htmlspecialchars($message['user_name']); ?></div>
                        <?php endif; ?>
                        <p class="message-content"><?php echo htmlspecialchars($message['message']); ?></p>
                        <div class="message-time">
                            <?php echo date('H:i', strtotime($message['created_at'])); ?>
                            <?php if ($message['user_id'] == $current_user_id): ?>✓<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Chat Input Area -->
            <div class="chat-input-area">
                <?php if ($is_member): ?>
                    <form method="POST" class="d-flex w-100" id="messageForm">
                        <input type="text" class="chat-input" name="message" placeholder="Type a message" id="messageInput" required>
                        <input type="hidden" name="send_message" value="1">
                        <button type="submit" class="send-btn" id="sendMessage">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <input type="text" class="chat-input" placeholder="Join group to send messages" disabled>
                    <button class="send-btn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto refresh messages every 3 seconds
        function refreshMessages() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newMessages = doc.querySelectorAll('#messagesContainer .message');
                    const currentMessages = document.querySelectorAll('#messagesContainer .message');
                    
                    // Only update if there are new messages
                    if (newMessages.length > currentMessages.length) {
                        document.getElementById('messagesContainer').innerHTML = doc.getElementById('messagesContainer').innerHTML;
                        scrollToBottom();
                    }
                })
                .catch(error => console.error('Error refreshing messages:', error));
        }

        // Scroll to bottom of messages
        function scrollToBottom() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Search messages
        function searchMessages() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const messages = document.querySelectorAll('.message');
            
            messages.forEach(message => {
                const content = message.querySelector('.message-content').textContent.toLowerCase();
                if (content.includes(searchTerm)) {
                    message.style.backgroundColor = 'rgba(255, 255, 0, 0.2)';
                    message.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    message.style.backgroundColor = '';
                }
            });
        }

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const chatArea = document.querySelector('.chat-area');
            
            sidebar.classList.toggle('active');
            chatArea.classList.toggle('hidden');
        }

        // Event Listeners
        document.getElementById('searchInput').addEventListener('input', searchMessages);
        document.getElementById('searchBtn').addEventListener('click', function() {
            document.getElementById('searchInput').focus();
        });
        document.getElementById('toggleSidebar').addEventListener('click', toggleSidebar);

        // Auto-refresh messages
        setInterval(refreshMessages, 3000);

        // Scroll to bottom on page load
        window.addEventListener('load', scrollToBottom);

        // Responsive behavior
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const chatArea = document.querySelector('.chat-area');
            
            if (window.innerWidth > 768) {
                sidebar.classList.add('active');
                chatArea.classList.remove('hidden');
            }
        });

        // Go back to previous page
function goBack() {
    // Use the PHP variable for the back URL
    window.location.href = '<?php echo $previous_page; ?>';
}

// Alternative: Use browser history if available
function goBackAlternative() {
    if (document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
        window.history.back();
    } else {
        window.location.href = '../komunitas/index.php';
    }
}
    </script>
</body>
</html>