<?php
// messages.php

// This page allows logged-in users to view their conversations and chat with other users in real time.

require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php';
require_login(); // Make sure the user is logged in

// Get information about the currently logged-in user
$me = get_user_by_id($pdo, $_SESSION['User_ID']);


//Fetch a list of users you’ve recently chatted with or all users if no messages
$stmt = $pdo->prepare("
  SELECT
    u.UserID,
    u.full_name,
    u.profile_pic,
    MAX(m.created_at) AS last_msg
  FROM users u
  LEFT JOIN messages m
    ON (u.UserID = m.sender_id OR u.UserID = m.receiver_id)
  WHERE u.UserID != ?
  GROUP BY u.UserID
  ORDER BY last_msg DESC, u.full_name ASC
  LIMIT 50
");
$stmt->execute([$me['UserID']]);
$partners = $stmt->fetchAll();


// If a conversation partner is selected, load messages
$partner_id = isset($_GET['with']) ? (int)$_GET['with'] : null;
$conversation = [];

if ($partner_id) {
    $stmt = $pdo->prepare("
        SELECT
            m.*,
            s.full_name AS sender_name
        FROM messages m
        JOIN users s ON m.sender_id = s.UserID
        WHERE
            (m.sender_id = ? AND m.receiver_id = ?)
            OR
            (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$me['UserID'], $partner_id, $partner_id, $me['UserID']]);
    $conversation = $stmt->fetchAll();
}


// Display the messages page
include 'header.php';
?>

<div class="messages-wrapper">
  <!-- Sidebar: list of conversations -->
  <aside class="conversations">
    <h3>Conversations</h3>

    <!-- Search input -->
    <input type="text" id="userSearch" placeholder="Search users..." style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;">

    <div id="userList">
      <?php foreach ($partners as $p): ?>
      <a
        class="partner <?= ($partner_id == $p['UserID']) ? 'active' : '' ?>"
        href="?with=<?= e($p['UserID']) ?>"
      >
        <!-- Show profile picture or default icon -->
        <img 
          src="<?= e($p['profile_pic'] ? 'uploads/'.$p['profile_pic'] : 'uploads/default.png') ?>" 
          width="40" 
          alt="Profile picture of <?= e($p['full_name']) ?>"
        >
        <?= e($p['full_name']) ?>
      </a>
    <?php endforeach; ?>
  </aside>


  <!-- Main Chat Area -->
  <section class="chat">
    <?php if ($partner_id): ?>

      <!-- Show message history -->
      <?php foreach ($conversation as $m): ?>
        <div class="bubble <?= $m['sender_id'] == $me['UserID'] ? 'sent' : 'received' ?>">
          <p><?= nl2br(e($m['body'])) ?></p>
          <time><?= e($m['created_at']) ?></time>
        </div>
      <?php endforeach; ?>

      <!-- Message sending form -->
      <form id="messageForm" action="send_message.php" method="post">
        <input type="hidden" name="receiver_id" value="<?= e($partner_id) ?>">
        <textarea
          name="body"
          id="messageBody"
          required
          placeholder="Type a message..."
        ></textarea>
        <button type="submit">Send</button>
      </form>

    <?php else: ?>
      <!-- Display when no conversation is selected -->
      <p>Select a conversation or search for a user to start messaging.</p>
    <?php endif; ?>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageBody = document.getElementById('messageBody');
    const chatSection = document.querySelector('.chat');
    const userSearch = document.getElementById('userSearch');
    const userList = document.getElementById('userList');

    // Function to load messages
    function loadMessages() {
        if (!<?= $partner_id ? 'true' : 'false' ?>) return;

        fetch('fetch_messages.php?receiver_id=<?= $partner_id ?>')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Clear existing messages
                const existingBubbles = chatSection.querySelectorAll('.bubble');
                existingBubbles.forEach(bubble => bubble.remove());

                // Add new messages
                data.forEach(msg => {
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble ' + (msg.sender_id == <?= $me['UserID'] ?> ? 'sent' : 'received');
                    bubble.innerHTML = `
                        <p>${msg.body.replace(/\n/g, '<br>')}</p>
                        <time>${msg.created_at}</time>
                    `;
                    chatSection.insertBefore(bubble, messageForm);
                });
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    // Handle form submission
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    messageBody.value = '';
                    loadMessages();
                } else {
                    console.error('Failed to send message');
                }
            })
            .catch(error => console.error('Error sending message:', error));
        });
    }

    // Handle user search
    userSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const partners = userList.querySelectorAll('.partner');

        partners.forEach(partner => {
            const name = partner.textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                partner.style.display = 'block';
            } else {
                partner.style.display = 'none';
            }
        });
    });

    // Load messages initially and periodically
    loadMessages();
    setInterval(loadMessages, 5000); // Refresh every 5 seconds
});
</script>

<?php include 'footer.php'; ?>
