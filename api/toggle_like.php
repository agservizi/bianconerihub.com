<?php
/**
 * API per aggiungere/rimuovere like a un post
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

// Verifichiamo se abbiamo ricevuto l'ID del post
if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID post non specificato']);
    exit;
}

// Otteniamo i dati
$postId = intval($_POST['post_id']);
$userId = $_SESSION['user_id'];

// Verifichiamo se il post esiste
$postCheck = $conn->query("SELECT user_id, likes_count FROM posts WHERE id = {$postId}");
if ($postCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Post non trovato']);
    exit;
}

$post = $postCheck->fetch_assoc();

// Verifichiamo se l'utente ha già messo like
$likeCheck = $conn->query("SELECT id FROM likes WHERE post_id = {$postId} AND user_id = {$userId}");
$hasLiked = ($likeCheck->num_rows > 0);

// Transazione per garantire che tutto vada a buon fine
$conn->begin_transaction();

try {
    if ($hasLiked) {
        // Rimuoviamo il like
        $conn->query("DELETE FROM likes WHERE post_id = {$postId} AND user_id = {$userId}");
        
        // Aggiorniamo il contatore dei like nel post
        $newLikesCount = $post['likes_count'] - 1;
        $conn->query("UPDATE posts SET likes_count = {$newLikesCount} WHERE id = {$postId}");
        
        // Se esisteva una notifica per questo like, la rimuoviamo
        if ($userId != $post['user_id']) {
            $conn->query("DELETE FROM notifications WHERE user_id = {$post['user_id']} AND sender_id = {$userId} AND type = 'like' AND reference_id = {$postId}");
        }
    } else {
        // Aggiungiamo il like
        $conn->query("INSERT INTO likes (post_id, user_id) VALUES ({$postId}, {$userId})");
        
        // Aggiorniamo il contatore dei like nel post
        $newLikesCount = $post['likes_count'] + 1;
        $conn->query("UPDATE posts SET likes_count = {$newLikesCount} WHERE id = {$postId}");
        
        // Creiamo una notifica per il proprietario del post (se non è l'utente stesso)
        if ($userId != $post['user_id']) {
            // Otteniamo il nome utente per la notifica
            $userResult = $conn->query("SELECT username FROM users WHERE id = {$userId}");
            $user = $userResult->fetch_assoc();
            
            $notificationContent = "@{$user['username']} ha messo mi piace al tuo post";
            
            $conn->query("INSERT INTO notifications (user_id, sender_id, type, content, reference_id) 
                          VALUES ({$post['user_id']}, {$userId}, 'like', '{$notificationContent}', {$postId})");
        }
    }
    
    // Commit della transazione
    $conn->commit();
    
    // Risposta di successo
    echo json_encode([
        'success' => true, 
        'has_liked' => !$hasLiked,
        'likes_count' => $newLikesCount
    ]);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
