<?php
session_start();
require_once 'auth/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_session = $_SESSION['user'];
$current_user_id = $user_session['id'];

// Get user's conversations
$conversations_query = "
    SELECT c.*, 
           u1.name as user1_name, u1.profile_photo as user1_photo,
           u2.name as user2_name, u2.profile_photo as user2_photo,
           m.message as last_message, m.created_at as last_message_time
    FROM conversations c
    JOIN users u1 ON c.user1_id = u1.id
    JOIN users u2 ON c.user2_id = u2.id
    LEFT JOIN messages m ON m.id = (
        SELECT id FROM messages 
        WHERE conversation_id = c.id 
        ORDER BY created_at DESC 
        LIMIT 1
    )
    WHERE c.user1_id = ? OR c.user2_id = ?
    ORDER BY c.updated_at DESC
";

$conversations = [];
if ($stmt = mysqli_prepare($conn, $conversations_query)) {
    mysqli_stmt_bind_param($stmt, "ii", $current_user_id, $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $conversations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Percakapan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .conversation-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 15px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: background 0.2s;
        }
        .conversation-item:hover {
            background: #f8f9fa;
        }
        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .last-message {
            color: #666;
            font-size: 0.9em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .unread-badge {
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <!-- Include your navbar here -->
    
    <div class="container mt-4">
        <h2>Percakapan Saya</h2>
        
        <?php if (empty($conversations)): ?>
            <div class="text-center mt-5">
                <i class="bi bi-chat-dots" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Belum ada percakapan</p>
                <a href="../index.php#cari-teman" class="btn btn-primary">Cari Teman</a>
            </div>
        <?php else: ?>
            <div class="list-group mt-3">
                <?php foreach ($conversations as $conv): 
                    $other_user = $conv['user1_id'] == $current_user_id ? [
                        'id' => $conv['user2_id'],
                        'name' => $conv['user2_name'],
                        'photo' => $conv['user2_photo']
                    ] : [
                        'id' => $conv['user1_id'],
                        'name' => $conv['user1_name'],
                        'photo' => $conv['user1_photo']
                    ];
                ?>
                    <a href="chat.php?user_id=<?php echo $other_user['id']; ?>" class="conversation-item">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo !empty($other_user['photo']) ? 'auth/' . htmlspecialchars($other_user['photo']) : 'assets/img/default-user.png'; ?>" 
                                 class="conversation-avatar me-3" alt="<?php echo htmlspecialchars($other_user['name']); ?>">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($other_user['name']); ?></h6>
                                <p class="last-message mb-0">
                                    <?php echo $conv['last_message'] ? htmlspecialchars($conv['last_message']) : 'Belum ada pesan'; ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    <?php echo $conv['last_message_time'] ? date('d M H:i', strtotime($conv['last_message_time'])) : ''; ?>
                                </small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>