<?php
/**
 * Pagina dei messaggi
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per visualizzare i messaggi', 'error');
    redirect(SITE_URL . '/login.php');
}

$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Verifichiamo se è specificato un utente specifico per visualizzare la conversazione
$conversationWithId = isset($_GET['user']) ? intval($_GET['user']) : 0;

// Otteniamo tutte le conversazioni dell'utente
$conversationsQuery = "SELECT 
                        u.id, u.username, u.full_name, u.profile_pic,
                        m.content as last_message, m.created_at as last_message_time,
                        m.sender_id, m.is_read,
                        (SELECT COUNT(*) FROM messages 
                         WHERE ((sender_id = u.id AND receiver_id = {$userId}) OR (sender_id = {$userId} AND receiver_id = u.id))) as message_count
                      FROM users u
                      JOIN messages m ON (m.sender_id = u.id AND m.receiver_id = {$userId}) OR (m.sender_id = {$userId} AND m.receiver_id = u.id)
                      WHERE m.id IN (
                          SELECT MAX(id) FROM messages 
                          WHERE (sender_id = {$userId} AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = {$userId})
                          GROUP BY IF(sender_id = {$userId}, receiver_id, sender_id)
                      )
                      AND u.id != {$userId}
                      ORDER BY m.created_at DESC";

$conversationsResult = $conn->query($conversationsQuery);

// Otteniamo i messaggi non letti
$unreadQuery = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = {$userId} AND is_read = 0";
$unreadResult = $conn->query($unreadQuery);
$unreadCount = ($unreadResult && $unreadResult->num_rows > 0) ? $unreadResult->fetch_assoc()['unread_count'] : 0;

// Se è specificato un utente, otteniamo i dettagli dell'utente e i messaggi della conversazione
$conversationUser = null;
$messages = null;

if ($conversationWithId > 0) {
    // Otteniamo i dettagli dell'utente
    $userQuery = "SELECT id, username, full_name, profile_pic FROM users WHERE id = {$conversationWithId}";
    $userResult = $conn->query($userQuery);
    
    if ($userResult && $userResult->num_rows > 0) {
        $conversationUser = $userResult->fetch_assoc();
        
        // Otteniamo i messaggi della conversazione
        $messagesQuery = "SELECT m.*, u.username, u.profile_pic 
                         FROM messages m 
                         JOIN users u ON m.sender_id = u.id
                         WHERE (m.sender_id = {$userId} AND m.receiver_id = {$conversationWithId}) 
                         OR (m.sender_id = {$conversationWithId} AND m.receiver_id = {$userId}) 
                         ORDER BY m.created_at ASC";
        
        $messagesResult = $conn->query($messagesQuery);
        
        if ($messagesResult) {
            $messages = [];
            while ($message = $messagesResult->fetch_assoc()) {
                $messages[] = $message;
            }
            
            // Segniamo i messaggi ricevuti come letti
            if ($messages) {
                $conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = {$conversationWithId} AND receiver_id = {$userId} AND is_read = 0");
            }
        }
    }
}
?>

<div class="container messages-container">
    <div class="row">
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope"></i> Messaggi
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger float-end"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush conversation-list">
                        <?php if ($conversationsResult && $conversationsResult->num_rows > 0): ?>
                            <?php while ($conversation = $conversationsResult->fetch_assoc()): ?>
                                <a href="<?php echo SITE_URL; ?>/messages.php?user=<?php echo $conversation['id']; ?>" class="list-group-item list-group-item-action <?php echo ($conversationWithId == $conversation['id']) ? 'active' : ''; ?>">
                                    <div class="d-flex">
                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $conversation['profile_pic']; ?>" alt="<?php echo $conversation['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-0"><?php echo $conversation['full_name']; ?></h6>
                                                <small class="text-muted"><?php echo formatDate($conversation['last_message_time'], 'd/m H:i'); ?></small>
                                            </div>
                                            <div class="text-truncate">
                                                <small class="<?php echo (!$conversation['is_read'] && $conversation['sender_id'] != $userId) ? 'fw-bold' : 'text-muted'; ?>">
                                                    <?php echo ($conversation['sender_id'] == $userId ? 'Tu: ' : ''); ?>
                                                    <?php echo htmlspecialchars(truncateText($conversation['last_message'], 30)); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <?php if (!$conversation['is_read'] && $conversation['sender_id'] != $userId): ?>
                                            <span class="badge bg-primary rounded-pill align-self-center">Nuovo</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center">
                                <p class="mb-0">Non hai ancora messaggi.</p>
                                <small class="text-muted">Inizia a seguire altri utenti per avviare conversazioni!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                        <i class="fas fa-edit"></i> Nuovo messaggio
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 col-lg-9">
            <div class="card">
                <?php if ($conversationUser): ?>
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $conversationUser['profile_pic']; ?>" alt="<?php echo $conversationUser['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <h5 class="card-title mb-0"><?php echo $conversationUser['full_name']; ?></h5>
                                <small class="text-muted">@<?php echo $conversationUser['username']; ?></small>
                            </div>
                            <div class="ms-auto">
                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $conversationUser['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user"></i> Profilo
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body messages-body">
                        <div class="messages-container">
                            <?php if ($messages && count($messages) > 0): ?>
                                <?php foreach ($messages as $message): ?>
                                    <div class="message <?php echo ($message['sender_id'] == $userId) ? 'message-sent' : 'message-received'; ?>">
                                        <div class="message-content">
                                            <div class="message-text">
                                                <?php echo nl2br(htmlspecialchars($message['content'])); ?>
                                            </div>
                                            <div class="message-time">
                                                <?php echo formatDate($message['created_at'], 'd/m H:i'); ?>
                                            </div>
                                        </div>
                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $message['profile_pic']; ?>" alt="<?php echo $message['username']; ?>" class="message-avatar rounded-circle" width="32" height="32">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center my-5">
                                    <p class="text-muted">Nessun messaggio ancora. Inizia la conversazione!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="<?php echo SITE_URL; ?>/actions/send_message.php" method="POST" id="messageForm">
                            <input type="hidden" name="receiver_id" value="<?php echo $conversationUser['id']; ?>">
                            <div class="input-group">
                                <textarea class="form-control" name="content" placeholder="Scrivi un messaggio..." rows="1" required></textarea>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card-body text-center">
                        <div class="my-5">
                            <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
                            <h4>Seleziona una conversazione</h4>
                            <p class="text-muted">Seleziona una conversazione esistente o avvia una nuova per iniziare a chattare.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                <i class="fas fa-edit"></i> Nuovo messaggio
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal per nuovo messaggio -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">Nuovo messaggio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo SITE_URL; ?>/actions/send_message.php" method="POST">
                    <div class="mb-3">
                        <label for="recipient" class="form-label">Destinatario</label>
                        <select class="form-select" name="receiver_id" id="recipient" required>
                            <option value="">Seleziona un utente</option>
                            <?php
                            // Otteniamo gli utenti che l'utente corrente segue
                            $followingQuery = "SELECT u.id, u.username, u.full_name 
                                             FROM users u 
                                             JOIN friendships f ON u.id = f.following_id 
                                             WHERE f.follower_id = {$userId} AND f.status = 'accepted'
                                             ORDER BY u.full_name";
                            
                            $followingResult = $conn->query($followingQuery);
                            
                            if ($followingResult && $followingResult->num_rows > 0) {
                                while ($following = $followingResult->fetch_assoc()) {
                                    echo "<option value=\"{$following['id']}\">{$following['full_name']} (@{$following['username']})</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Messaggio</label>
                        <textarea class="form-control" name="content" id="messageContent" rows="3" required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Invia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.messages-container {
    padding: 20px 0;
}

.messages-body {
    height: 500px;
    overflow-y: auto;
}

.messages-container {
    display: flex;
    flex-direction: column;
    padding: 15px;
}

.message {
    display: flex;
    margin-bottom: 15px;
    align-items: flex-end;
}

.message-sent {
    justify-content: flex-end;
}

.message-received {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    border-radius: 12px;
    padding: 8px 12px;
    position: relative;
}

.message-sent .message-content {
    background-color: #0a58ca;
    color: white;
    margin-right: 10px;
    border-bottom-right-radius: 0;
}

.message-received .message-content {
    background-color: #f0f2f5;
    color: #212529;
    margin-left: 10px;
    border-bottom-left-radius: 0;
}

.message-text {
    margin-bottom: 2px;
}

.message-time {
    font-size: 0.7rem;
    opacity: 0.8;
    text-align: right;
}

.message-avatar {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.conversation-list .list-group-item.active {
    background-color: #0a58ca;
    border-color: #0a58ca;
}

textarea[name="content"] {
    resize: none;
    overflow: hidden;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll alla fine della conversazione
    const messagesBody = document.querySelector('.messages-body');
    if (messagesBody) {
        messagesBody.scrollTop = messagesBody.scrollHeight;
    }
    
    // Auto-espandi textarea
    const textarea = document.querySelector('textarea[name="content"]');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Invia con Enter (ma Shift+Enter per nuova linea)
    if (textarea) {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('messageForm').submit();
            }
        });
    }
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
