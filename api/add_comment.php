<?php
/**
 * API per aggiungere commenti ai post
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

// Verifichiamo se abbiamo ricevuto i dati necessari
if (!isset($_POST['post_id']) || empty($_POST['post_id']) || !isset($_POST['content']) || empty($_POST['content'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

// Otteniamo i dati
$postId = intval($_POST['post_id']);
$userId = $_SESSION['user_id'];
$content = sanitizeInput($_POST['content']);

// Limita la lunghezza del commento
if (strlen($content) > 500) {
    $content = substr($content, 0, 500);
}

// Verifichiamo se il post esiste
$postCheck = $conn->query("SELECT user_id, comments_count FROM posts WHERE id = {$postId}");
if ($postCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Post non trovato']);
    exit;
}

$post = $postCheck->fetch_assoc();

// Otteniamo i dati dell'utente
$userQuery = $conn->query("SELECT username, profile_pic FROM users WHERE id = {$userId}");
$user = $userQuery->fetch_assoc();

// Transazione per garantire che tutto vada a buon fine
$conn->begin_transaction();

try {
    // Inseriamo il commento
    $insertQuery = "INSERT INTO comments (post_id, user_id, content) VALUES ({$postId}, {$userId}, '{$content}')";
    $conn->query($insertQuery);
    $commentId = $conn->insert_id;
    
    // Aggiorniamo il contatore dei commenti nel post
    $newCommentsCount = $post['comments_count'] + 1;
    $conn->query("UPDATE posts SET comments_count = {$newCommentsCount} WHERE id = {$postId}");
    
    // Creiamo una notifica per il proprietario del post (se non è l'utente stesso)
    if ($userId != $post['user_id']) {
        $notificationContent = "@{$user['username']} ha commentato il tuo post";
        
        $conn->query("INSERT INTO notifications (user_id, sender_id, type, content, reference_id) 
                      VALUES ({$post['user_id']}, {$userId}, 'comment', '{$notificationContent}', {$postId})");
    }
    
    // Cerca menzioni nel commento e crea notifiche (@username)
    preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $mentions);
    if (!empty($mentions[1])) {
        foreach ($mentions[1] as $mentionedUsername) {
            // Otteniamo l'ID dell'utente menzionato
            $mentionedQuery = $conn->query("SELECT id FROM users WHERE username = '{$mentionedUsername}'");
            
            if ($mentionedQuery->num_rows > 0) {
                $mentioned = $mentionedQuery->fetch_assoc();
                $mentionedId = $mentioned['id'];
                
                // Verifichiamo che non sia l'utente stesso o il proprietario del post (già notificato)
                if ($mentionedId != $userId && $mentionedId != $post['user_id']) {
                    $notificationContent = "@{$user['username']} ti ha menzionato in un commento";
                    
                    $conn->query("INSERT INTO notifications (user_id, sender_id, type, content, reference_id) 
                                 VALUES ({$mentionedId}, {$userId}, 'mention', '{$notificationContent}', {$postId})");
                }
            }
        }
    }
    
    // Commit della transazione
    $conn->commit();
    
    // Risposta di successo
    echo json_encode([
        'success' => true,
        'comment_id' => $commentId,
        'user_id' => $userId,
        'username' => $user['username'],
        'profile_pic' => $user['profile_pic'],
        'content' => $content,
        'comments_count' => $newCommentsCount
    ]);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
