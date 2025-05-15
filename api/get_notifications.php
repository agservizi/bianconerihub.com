<?php
/**
 * API per ottenere le notifiche non lette
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

$userId = $_SESSION['user_id'];

// Otteniamo le notifiche non lette
$query = "SELECT n.id, n.content, n.type, n.reference_id, n.created_at, u.username, u.profile_pic 
          FROM notifications n 
          JOIN users u ON n.sender_id = u.id 
          WHERE n.user_id = {$userId} AND n.is_read = 0 
          ORDER BY n.created_at DESC 
          LIMIT 5";

$result = $conn->query($query);
$notifications = [];

if ($result && $result->num_rows > 0) {
    while ($notification = $result->fetch_assoc()) {
        // Determiniamo l'URL di destinazione in base al tipo di notifica
        $url = SITE_URL;
        
        switch ($notification['type']) {
            case 'like':
            case 'comment':
            case 'mention':
                if (!empty($notification['reference_id'])) {
                    $url = SITE_URL . '/post.php?id=' . $notification['reference_id'];
                }
                break;
            case 'follow':
                $url = SITE_URL . '/profile.php?id=' . $notification['sender_id'];
                break;
            case 'system':
                // Per notifiche di messaggi, reindirizzamento alla chat
                if (strpos($notification['content'], 'messaggio') !== false) {
                    $url = SITE_URL . '/messages.php?user=' . $notification['sender_id'];
                }
                break;
        }
        
        // Calcoliamo il tempo trascorso
        $timestamp = strtotime($notification['created_at']);
        $timeAgo = timeAgo($timestamp);
        
        $notifications[] = [
            'id' => $notification['id'],
            'content' => $notification['content'],
            'url' => $url,
            'time_ago' => $timeAgo,
            'profile_pic' => $notification['profile_pic']
        ];
    }
}

// Otteniamo il conteggio totale di notifiche non lette
$countQuery = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = {$userId} AND is_read = 0";
$countResult = $conn->query($countQuery);
$unreadCount = ($countResult && $countResult->num_rows > 0) ? $countResult->fetch_assoc()['unread_count'] : 0;

// Risposta
echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'unread_count' => $unreadCount
]);

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
