<?php
/**
 * API per caricare nuovi messaggi
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Verifichiamo se abbiamo ricevuto l'ID dell'utente e l'ID dell'ultimo messaggio
if (!isset($_GET['user_id']) || empty($_GET['user_id']) || !isset($_GET['last_id']) || empty($_GET['last_id'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit;
}

// Otteniamo i dati
$userId = $_SESSION['user_id'];
$conversationWith = intval($_GET['user_id']);
$lastMessageId = intval($_GET['last_id']);

// Verifichiamo se l'utente esiste
$userCheck = $conn->query("SELECT id FROM users WHERE id = {$conversationWith}");
if ($userCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
    exit;
}

// Otteniamo i nuovi messaggi
$messagesQuery = "SELECT m.*, u.username, u.profile_pic 
                 FROM messages m 
                 JOIN users u ON m.sender_id = u.id
                 WHERE m.id > {$lastMessageId}
                 AND ((m.sender_id = {$userId} AND m.receiver_id = {$conversationWith}) 
                     OR (m.sender_id = {$conversationWith} AND m.receiver_id = {$userId})) 
                 ORDER BY m.created_at ASC";

$messagesResult = $conn->query($messagesQuery);

$newMessages = [];

if ($messagesResult && $messagesResult->num_rows > 0) {
    while ($message = $messagesResult->fetch_assoc()) {
        // Segniamo i messaggi ricevuti come letti
        if ($message['sender_id'] == $conversationWith && $message['is_read'] == 0) {
            $conn->query("UPDATE messages SET is_read = 1 WHERE id = {$message['id']}");
        }
        
        $newMessages[] = [
            'id' => $message['id'],
            'content' => nl2br(htmlspecialchars($message['content'])),
            'sender_id' => $message['sender_id'],
            'is_sent' => ($message['sender_id'] == $userId),
            'username' => $message['username'],
            'profile_pic' => $message['profile_pic'],
            'time' => formatDate($message['created_at'], 'd/m H:i')
        ];
    }
}

// Aggiorniamo anche il conteggio dei messaggi non letti
$unreadQuery = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = {$userId} AND is_read = 0";
$unreadResult = $conn->query($unreadQuery);
$unreadCount = ($unreadResult && $unreadResult->num_rows > 0) ? $unreadResult->fetch_assoc()['unread_count'] : 0;

// Risposta
echo json_encode([
    'success' => true,
    'messages' => $newMessages,
    'unread_count' => $unreadCount
]);
?>
