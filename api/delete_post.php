<?php
/**
 * API per eliminare un post
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

// Verifichiamo se il post esiste e appartiene all'utente
$postCheck = $conn->query("SELECT id, user_id, media FROM posts WHERE id = {$postId}");

if ($postCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Post non trovato']);
    exit;
}

$post = $postCheck->fetch_assoc();

// Verifichiamo se l'utente è il proprietario del post o un admin
if ($post['user_id'] != $userId && !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo post']);
    exit;
}

// Transazione per garantire che tutto vada a buon fine
$conn->begin_transaction();

try {
    // Eliminiamo il post
    $conn->query("DELETE FROM posts WHERE id = {$postId}");
    
    // Eliminiamo tutti i commenti del post
    $conn->query("DELETE FROM comments WHERE post_id = {$postId}");
    
    // Eliminiamo tutti i like del post
    $conn->query("DELETE FROM likes WHERE post_id = {$postId}");
    
    // Eliminiamo tutte le notifiche correlate al post
    $conn->query("DELETE FROM notifications WHERE reference_id = {$postId} AND (type = 'like' OR type = 'comment' OR type = 'mention')");
    
    // Eliminiamo l'immagine associata, se presente
    if (!empty($post['media'])) {
        $mediaPath = ROOT_PATH . '/uploads/posts/' . $post['media'];
        if (file_exists($mediaPath)) {
            unlink($mediaPath);
        }
    }
    
    // Commit della transazione
    $conn->commit();
    
    // Risposta di successo
    echo json_encode(['success' => true, 'message' => 'Post eliminato con successo']);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
