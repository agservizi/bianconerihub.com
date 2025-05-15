<?php
/**
 * API per seguire/smettere di seguire un utente
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

// Verifichiamo se abbiamo ricevuto l'ID dell'utente
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID utente non specificato']);
    exit;
}

// Otteniamo i dati
$followingId = intval($_POST['user_id']);
$followerId = $_SESSION['user_id'];

// Controllo che non si stia cercando di seguire se stessi
if ($followerId == $followingId) {
    echo json_encode(['success' => false, 'message' => 'Non puoi seguire te stesso']);
    exit;
}

// Verifichiamo se l'utente da seguire esiste
$userCheck = $conn->query("SELECT id, username FROM users WHERE id = {$followingId}");
if ($userCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
    exit;
}

$user = $userCheck->fetch_assoc();

// Verifichiamo se già segue l'utente
$followCheck = $conn->query("SELECT id, status FROM friendships WHERE follower_id = {$followerId} AND following_id = {$followingId}");
$isFollowing = ($followCheck->num_rows > 0);

// Transazione per garantire che tutto vada a buon fine
$conn->begin_transaction();

try {
    if ($isFollowing) {
        $follow = $followCheck->fetch_assoc();
        
        // Rimuoviamo la relazione
        $conn->query("DELETE FROM friendships WHERE id = {$follow['id']}");
        
        // Rimuoviamo eventuali notifiche correlate
        $conn->query("DELETE FROM notifications WHERE user_id = {$followingId} AND sender_id = {$followerId} AND type = 'follow'");
        
        $isNowFollowing = false;
    } else {
        // Aggiungiamo la relazione (auto-accettata)
        $conn->query("INSERT INTO friendships (follower_id, following_id, status) VALUES ({$followerId}, {$followingId}, 'accepted')");
        
        // Otteniamo il nome utente per la notifica
        $followerResult = $conn->query("SELECT username FROM users WHERE id = {$followerId}");
        $follower = $followerResult->fetch_assoc();
        
        // Creiamo una notifica per l'utente seguito
        $notificationContent = "@{$follower['username']} ha iniziato a seguirti";
        
        $conn->query("INSERT INTO notifications (user_id, sender_id, type, content) 
                      VALUES ({$followingId}, {$followerId}, 'follow', '{$notificationContent}')");
        
        $isNowFollowing = true;
    }
    
    // Commit della transazione
    $conn->commit();
    
    // Risposta di successo
    echo json_encode([
        'success' => true, 
        'is_following' => $isNowFollowing,
        'username' => $user['username']
    ]);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
