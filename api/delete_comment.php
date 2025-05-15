<?php
/**
 * API per eliminare un commento
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

// Verifichiamo se abbiamo ricevuto l'ID del commento
if (!isset($_POST['comment_id']) || empty($_POST['comment_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID commento non specificato']);
    exit;
}

// Otteniamo i dati
$commentId = intval($_POST['comment_id']);
$userId = $_SESSION['user_id'];

// Verifichiamo se il commento esiste
$commentCheck = $conn->query("SELECT c.id, c.user_id, c.post_id, p.comments_count 
                             FROM comments c 
                             JOIN posts p ON c.post_id = p.id 
                             WHERE c.id = {$commentId}");

if ($commentCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Commento non trovato']);
    exit;
}

$comment = $commentCheck->fetch_assoc();

// Verifichiamo se l'utente è il proprietario del commento o un admin
if ($comment['user_id'] != $userId && !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo commento']);
    exit;
}

// Transazione per garantire che tutto vada a buon fine
$conn->begin_transaction();

try {
    // Eliminiamo il commento
    $conn->query("DELETE FROM comments WHERE id = {$commentId}");
    
    // Aggiorniamo il contatore dei commenti nel post
    $newCommentsCount = $comment['comments_count'] - 1;
    $conn->query("UPDATE posts SET comments_count = {$newCommentsCount} WHERE id = {$comment['post_id']}");
    
    // Eliminiamo tutte le notifiche correlate al commento
    $conn->query("DELETE FROM notifications WHERE reference_id = {$comment['post_id']} AND sender_id = {$userId} AND type = 'comment'");
    
    // Commit della transazione
    $conn->commit();
    
    // Risposta di successo
    echo json_encode([
        'success' => true, 
        'message' => 'Commento eliminato con successo',
        'comments_count' => $newCommentsCount
    ]);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
