<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Mark Wayar</title>
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
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><rect width="40" height="40" fill="%23666"/><circle cx="20" cy="15" r="6" fill="white"/><path d="M20 25c-8 0-12 4-12 8v7h24v-7c0-4-4-8-12-8z" fill="white"/></svg>');
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
            <a href="#">BERANDA</a>
            <a href="#">TENTANG</a>
            <a href="#">CARI TEMAN</a>
            <a href="#">KOMUNITAS</a>
        </div>
        <div class="profile-icon">👤</div>
    </div>

    <!-- Chat Header -->
    <div class="chat-header">
        <div class="avatar"></div>
        <div class="chat-name">Mark Wayar</div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <div class="messages">
            <div class="message sent">
                <div class="message-bubble">Haii boleh kenalan ga sebelum booking?</div>
            </div>
            
            <div class="message sent">
                <div class="message-bubble">Kalo blm jago main basket masih mau nememin ga?</div>
            </div>
            
            <div class="message received">
                <div class="message-bubble">bolehh</div>
            </div>
            
            <div class="message received">
                <div class="message-bubble">santaii, ga harus jago kok, bahkan bisa gue ajarin main basket futsal kalo lu mau</div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="input-area">
            <input type="text" class="message-input" placeholder="Ketik Pesan" id="messageInput">
            <button class="send-button" onclick="sendMessage()">
                <svg viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const messageText = input.value.trim();
            
            if (messageText === '') return;
            
            const messagesContainer = document.querySelector('.messages');
            
            // Create new message element
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message sent';
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';
            bubbleDiv.textContent = messageText;
            
            messageDiv.appendChild(bubbleDiv);
            messagesContainer.appendChild(messageDiv);
            
            // Clear input
            input.value = '';
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Send message on Enter key
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>