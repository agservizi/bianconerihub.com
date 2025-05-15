<?php
/**
 * Script per l'invio di messaggi
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per inviare messaggi', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/messages.php');
}

// Otteniamo i dati
$senderId = $_SESSION['user_id'];
$receiverId = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$content = isset($_POST['content']) ? sanitizeInput($_POST['content']) : '';

// Validazione dati
if ($receiverId <= 0) {
    setFlashMessage('Destinatario non valido', 'error');
    redirect(SITE_URL . '/messages.php');
}

if (empty($content)) {
    setFlashMessage('Il messaggio non può essere vuoto', 'error');
    redirect(SITE_URL . '/messages.php?user=' . $receiverId);
}

// Limitiamo la lunghezza del messaggio
if (strlen($content) > 1000) {
    $content = substr($content, 0, 1000);
}

// Verifichiamo se il destinatario esiste
$receiverCheck = $conn->query("SELECT id FROM users WHERE id = {$receiverId}");
if ($receiverCheck->num_rows === 0) {
    setFlashMessage('Destinatario non trovato', 'error');
    redirect(SITE_URL . '/messages.php');
}

// Verifichiamo se l'utente può inviare messaggi al destinatario
// Per BiancoNeriHub, permettiamo l'invio di messaggi solo agli utenti che si seguono a vicenda
// o se uno dei due è amministratore
$canSendMessage = false;

if (isAdmin() || $receiverId == 1) { // L'admin può sempre inviare e ricevere messaggi
    $canSendMessage = true;
} else {
    // Verifichiamo se il mittente segue il destinatario
    $followCheck = $conn->query("SELECT id FROM friendships 
                                WHERE follower_id = {$senderId} AND following_id = {$receiverId} 
                                AND status = 'accepted'");
    
    // Verifichiamo se il destinatario segue il mittente
    $followBackCheck = $conn->query("SELECT id FROM friendships 
                                    WHERE follower_id = {$receiverId} AND following_id = {$senderId} 
                                    AND status = 'accepted'");
    
    $canSendMessage = ($followCheck->num_rows > 0 || $followBackCheck->num_rows > 0);
}

if (!$canSendMessage) {
    setFlashMessage('Non puoi inviare messaggi a questo utente. Assicurati che vi seguiate a vicenda.', 'error');
    redirect(SITE_URL . '/messages.php');
}

// Salviamo il messaggio
$query = "INSERT INTO messages (sender_id, receiver_id, content) 
          VALUES ({$senderId}, {$receiverId}, '{$content}')";

if ($conn->query($query)) {
    // Messaggio inviato con successo
    
    // Creiamo una notifica per il destinatario se non è già in conversazione
    $checkOpenConversation = $conn->query("SELECT created_at FROM messages 
                                          WHERE sender_id = {$receiverId} AND receiver_id = {$senderId} 
                                          ORDER BY created_at DESC 
                                          LIMIT 1");
    
    if ($checkOpenConversation->num_rows === 0 || 
        (time() - strtotime($checkOpenConversation->fetch_assoc()['created_at']) > 3600)) { // Se non ha risposto nell'ultima ora
        
        $currentUser = getCurrentUser();
        $notificationContent = "@{$currentUser['username']} ti ha inviato un messaggio";
        
        $conn->query("INSERT INTO notifications (user_id, sender_id, type, content) 
                     VALUES ({$receiverId}, {$senderId}, 'system', '{$notificationContent}')");
    }
    
    // Reindirizzamento alla conversazione
    redirect(SITE_URL . '/messages.php?user=' . $receiverId);
} else {
    setFlashMessage('Si è verificato un errore durante l\'invio del messaggio', 'error');
    redirect(SITE_URL . '/messages.php?user=' . $receiverId);
}
?>
