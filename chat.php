<?php
session_start();
require_once 'auth/config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_session = $_SESSION['user'];
$current_user_id = $user_session['id'];

// Get the other user ID from URL parameter
$other_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($other_user_id === 0) {
    header("Location: ../index.php");
    exit;
}

// Verify the other user exists
$user_query = "SELECT id, name, profile_photo FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $user_query)) {
    mysqli_stmt_bind_param($stmt, "i", $other_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $other_user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$other_user) {
    die("User tidak ditemukan.");
}

// Get or create conversation
$conversation_query = "SELECT id FROM conversations 
                      WHERE (user1_id = ? AND user2_id = ?) 
                      OR (user1_id = ? AND user2_id = ?)";
if ($stmt = mysqli_prepare($conn, $conversation_query)) {
    mysqli_stmt_bind_param($stmt, "iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $conversation = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$conversation) {
    // Create new conversation
    $create_conversation_query = "INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $create_conversation_query)) {
        $user1_id = min($current_user_id, $other_user_id);
        $user2_id = max($current_user_id, $other_user_id);
        mysqli_stmt_bind_param($stmt, "ii", $user1_id, $user2_id);
        mysqli_stmt_execute($stmt);
        $conversation_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }
} else {
    $conversation_id = $conversation['id'];
}

// Handle sending new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $insert_message_query = "INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $insert_message_query)) {
        mysqli_stmt_bind_param($stmt, "iis", $conversation_id, $current_user_id, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Update conversation updated_at
        $update_conversation_query = "UPDATE conversations SET updated_at = NOW() WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $update_conversation_query)) {
            mysqli_stmt_bind_param($stmt, "i", $conversation_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Return JSON response for AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

// Get all messages for this conversation
$messages_query = "SELECT m.*, u.name as sender_name, u.profile_photo as sender_photo 
                  FROM messages m 
                  JOIN users u ON m.sender_id = u.id 
                  WHERE m.conversation_id = ? 
                  ORDER BY m.created_at ASC";
$messages = [];
if ($stmt = mysqli_prepare($conn, $messages_query)) {
    mysqli_stmt_bind_param($stmt, "i", $conversation_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Mark messages as read
$mark_read_query = "UPDATE messages SET is_read = TRUE 
                   WHERE conversation_id = ? AND sender_id = ? AND is_read = FALSE";
if ($stmt = mysqli_prepare($conn, $mark_read_query)) {
    mysqli_stmt_bind_param($stmt, "ii", $conversation_id, $other_user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - <?php echo htmlspecialchars($other_user['name']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f5f5f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #ffd54f, #ffb74d);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .profile-icon {
            width: 32px;
            height: 32px;
            background: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        /* Chat Header */
        .chat-header {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #666;
            background-size: cover;
            background-position: center;
        }

        .chat-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        /* Chat Container */
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8f8f8;
        }

        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Message Bubbles */
        .message {
            display: flex;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message.received {
            align-self: flex-start;
        }

        .message.sent {
            align-self: flex-end;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
        }

        .message.received .message-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message.sent .message-bubble {
            background: #e3f2fd;
            color: #333;
            border-bottom-right-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message-time {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        /* Input Area */
        .input-area {
            background: white;
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .message-input {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 14px;
            outline: none;
            background: #f8f8f8;
        }

        .message-input:focus {
            border-color: #2196f3;
            background: white;
        }

        .send-button {
            width: 40px;
            height: 40px;
            border: none;
            background: #2196f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .send-button:hover {
            background: #1976d2;
        }

        .send-button svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .send-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .messages {
                padding: 15px;
            }
            
            .message {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="nav-links">
            <a href="index.php">BERANDA</a>
            <a href="index.php#tentang">TENTANG</a>
            <a href="index.php#cari-teman">CARI TEMAN</a>
            <a href="index.php#komunitas">KOMUNITAS</a>
        </div>
        <div class="profile-icon">
            <a href="auth/profil_user.php" style="color: white; text-decoration: none;">👤</a>
        </div>
    </div>

    <!-- Chat Header -->
    <div class="chat-header">
        <div class="avatar" style="background-image: url('<?php echo !empty($other_user['profile_photo']) ? 'auth/' . htmlspecialchars($other_user['profile_photo']) : 'assets/img/default-user.png'; ?>')"></div>
        <div class="chat-name"><?php echo htmlspecialchars($other_user['name']); ?></div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <div class="messages" id="messagesContainer">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $current_user_id ? 'sent' : 'received'; ?>">
                    <div class="message-bubble">
                        <?php echo htmlspecialchars($message['message']); ?>
                        <div class="message-time">
                            <?php echo date('H:i', strtotime($message['created_at'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Input Area -->
        <div class="input-area">
            <input type="text" class="message-input" placeholder="Ketik Pesan" id="messageInput">
            <button class="send-button" id="sendButton" onclick="sendMessage()">
                <svg viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        let isSending = false;

        function sendMessage() {
            if (isSending) return;
            
            const input = document.getElementById('messageInput');
            const messageText = input.value.trim();
            
            if (messageText === '') return;
            
            isSending = true;
            const sendButton = document.getElementById('sendButton');
            sendButton.disabled = true;
            
            // Create temporary message
            const messagesContainer = document.getElementById('messagesContainer');
            const tempMessageDiv = document.createElement('div');
            tempMessageDiv.className = 'message sent';
            tempMessageDiv.innerHTML = `
                <div class="message-bubble">
                    ${messageText}
                    <div class="message-time">Mengirim...</div>
                </div>
            `;
            messagesContainer.appendChild(tempMessageDiv);
            
            // Clear input
            input.value = '';
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Send to server
            fetch('chat.php?user_id=<?php echo $other_user_id; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'message=' + encodeURIComponent(messageText)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove temporary message and reload messages
                    tempMessageDiv.remove();
                    loadMessages();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                tempMessageDiv.querySelector('.message-bubble').innerHTML = `
                    ${messageText}
                    <div class="message-time" style="color: #f44336;">Gagal mengirim</div>
                `;
            })
            .finally(() => {
                isSending = false;
                sendButton.disabled = false;
            });
        }
        
        function loadMessages() {
            fetch('chat.php?user_id=<?php echo $other_user_id; ?>')
            .then(response => response.text())
            .then(html => {
                // Extract messages from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessages = doc.querySelectorAll('.message');
                
                const messagesContainer = document.getElementById('messagesContainer');
                messagesContainer.innerHTML = '';
                
                newMessages.forEach(message => {
                    messagesContainer.appendChild(message.cloneNode(true));
                });
                
                // Scroll to bottom
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading messages:', error);
            });
        }
        
        // Auto-refresh messages every 3 seconds
        setInterval(loadMessages, 3000);
        
        // Send message on Enter key
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        // Scroll to bottom on page load
        window.addEventListener('load', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
</body>
</html>