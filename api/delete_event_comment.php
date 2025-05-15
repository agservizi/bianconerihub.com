<?php
/**
 * API per eliminare i commenti degli eventi
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
    echo json_encode(['success' => false, 'message' => 'ID commento mancante']);
    exit;
}

// Otteniamo i dati
$commentId = intval($_POST['comment_id']);
$userId = $_SESSION['user_id'];

// Otteniamo i dettagli del commento
$commentQuery = "SELECT ec.*, e.created_by as event_creator_id 
                FROM event_comments ec
                JOIN events e ON ec.event_id = e.id
                WHERE ec.id = {$commentId}";

$commentResult = $conn->query($commentQuery);

if ($commentResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Commento non trovato']);
    exit;
}

$comment = $commentResult->fetch_assoc();

// Verifichiamo se l'utente ha i permessi per eliminare il commento
// (proprietario del commento, proprietario dell'evento o admin)
if ($comment['user_id'] != $userId && $comment['event_creator_id'] != $userId && !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per eliminare questo commento']);
    exit;
}

// Eliminiamo il commento
$deleteCommentQuery = "DELETE FROM event_comments WHERE id = {$commentId}";

if ($conn->query($deleteCommentQuery)) {
    echo json_encode(['success' => true, 'message' => 'Commento eliminato con successo']);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione del commento: ' . $conn->error]);
}
?>
