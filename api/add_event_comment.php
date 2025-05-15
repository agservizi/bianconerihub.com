<?php
/**
 * API per aggiungere commenti agli eventi
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

// Verifichiamo se abbiamo ricevuto l'ID dell'evento e il commento
if (!isset($_POST['event_id']) || empty($_POST['event_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit;
}

// Otteniamo i dati
$eventId = intval($_POST['event_id']);
$userId = $_SESSION['user_id'];
$comment = sanitizeInput($_POST['comment']);

// Verifichiamo se l'evento esiste
$eventQuery = "SELECT e.*, u.username, u.full_name FROM events e 
               JOIN users u ON e.created_by = u.id
               WHERE e.id = {$eventId}";

$eventResult = $conn->query($eventQuery);

if ($eventResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Evento non trovato']);
    exit;
}

$event = $eventResult->fetch_assoc();

// Aggiungiamo il commento
$insertCommentQuery = "INSERT INTO event_comments (event_id, user_id, comment, created_at) 
                      VALUES ({$eventId}, {$userId}, '{$comment}', NOW())";

if ($conn->query($insertCommentQuery)) {
    $commentId = $conn->insert_id;
    
    // Otteniamo i dettagli dell'utente
    $userQuery = "SELECT id, username, full_name, profile_pic FROM users WHERE id = {$userId}";
    $userResult = $conn->query($userQuery);
    $user = $userResult->fetch_assoc();
    
    // Creiamo il timestamp
    $timestamp = date('Y-m-d H:i:s');
    $timeAgo = timeAgo(strtotime($timestamp));
    
    // Creiamo l'HTML del commento per l'aggiornamento immediato dell'UI
    $commentHtml = '
    <div class="comment-item mb-3" id="comment-' . $commentId . '">
        <div class="d-flex">
            <a href="' . SITE_URL . '/profile.php?id=' . $user['id'] . '">
                <img src="' . UPLOADS_URL . '/profile_pics/' . $user['profile_pic'] . '" class="rounded-circle me-2" width="40" height="40" alt="' . $user['username'] . '">
            </a>
            <div class="flex-grow-1">
                <div class="card">
                    <div class="card-header bg-light py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="' . SITE_URL . '/profile.php?id=' . $user['id'] . '" class="text-decoration-none text-dark fw-bold">
                                    ' . htmlspecialchars($user['full_name']) . '
                                </a>
                                <span class="text-muted ms-2">@' . $user['username'] . '</span>
                            </div>
                            <small class="text-muted" title="' . date('d/m/Y H:i', strtotime($timestamp)) . '">
                                ' . $timeAgo . '
                            </small>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <p class="card-text mb-0">' . nl2br(htmlspecialchars($comment)) . '</p>
                    </div>
                    <div class="card-footer bg-white py-1 text-end">
                        <button class="btn btn-sm btn-danger delete-comment-btn" data-comment-id="' . $commentId . '">
                            <i class="fas fa-trash-alt"></i> Elimina
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    
    // Se il commento è stato aggiunto da un utente diverso dall'organizzatore, inviamo una notifica
    if ($userId != $event['created_by']) {
        $notificationQuery = "INSERT INTO notifications (user_id, sender_id, type, content, reference_id, created_at) 
                            VALUES ({$event['created_by']}, {$userId}, 'event_comment', 'ha commentato il tuo evento \"{$event['title']}\"', {$eventId}, NOW())";
        
        $conn->query($notificationQuery);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Commento aggiunto con successo', 
        'html' => $commentHtml,
        'comment_id' => $commentId
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiunta del commento: ' . $conn->error]);
}

/**
 * Funzione per formattare il tempo trascorso
 * 
 * @param int $timestamp Timestamp da formattare
 * @return string Tempo trascorso in formato leggibile
 */
function timeAgo($timestamp) {
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return $diff . " secondi fa";
    } else if ($diff < 3600) {
        return floor($diff / 60) . " minuti fa";
    } else if ($diff < 86400) {
        return floor($diff / 3600) . " ore fa";
    } else if ($diff < 604800) {
        return floor($diff / 86400) . " giorni fa";
    } else if ($diff < 2592000) {
        return floor($diff / 604800) . " settimane fa";
    } else if ($diff < 31536000) {
        return floor($diff / 2592000) . " mesi fa";
    } else {
        return floor($diff / 31536000) . " anni fa";
    }
}
?>
