<?php
/**
 * Script per l'aggiornamento della password
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per modificare la password', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/settings.php#account');
}

// Otteniamo i dati
$userId = $_SESSION['user_id'];
$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

// Validazione dati
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    setFlashMessage('Tutti i campi sono obbligatori', 'error');
    redirect(SITE_URL . '/settings.php#account');
}

if ($newPassword !== $confirmPassword) {
    setFlashMessage('Le nuove password non corrispondono', 'error');
    redirect(SITE_URL . '/settings.php#account');
}

if (strlen($newPassword) < 8) {
    setFlashMessage('La password deve contenere almeno 8 caratteri', 'error');
    redirect(SITE_URL . '/settings.php#account');
}

// Otteniamo la password attuale dell'utente
$query = "SELECT password FROM users WHERE id = {$userId}";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verifichiamo se la password attuale è corretta
    if (!password_verify($currentPassword, $user['password'])) {
        setFlashMessage('Password attuale non corretta', 'error');
        redirect(SITE_URL . '/settings.php#account');
    }
    
    // Generiamo l'hash della nuova password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Aggiorniamo la password
    $updateQuery = "UPDATE users SET password = '{$passwordHash}' WHERE id = {$userId}";
    
    if ($conn->query($updateQuery)) {
        setFlashMessage('Password aggiornata con successo', 'success');
    } else {
        setFlashMessage('Si è verificato un errore durante l\'aggiornamento della password: ' . $conn->error, 'error');
    }
} else {
    setFlashMessage('Utente non trovato', 'error');
}

// Reindirizzamento alla pagina delle impostazioni
redirect(SITE_URL . '/settings.php#account');
?>
