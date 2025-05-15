<?php
/**
 * Script per cancellare tutte le notifiche
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per gestire le notifiche', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/notifications.php');
}

$userId = $_SESSION['user_id'];

// Eliminiamo tutte le notifiche dell'utente
$query = "DELETE FROM notifications WHERE user_id = {$userId}";

if ($conn->query($query)) {
    setFlashMessage('Tutte le notifiche sono state cancellate', 'success');
} else {
    setFlashMessage('Si è verificato un errore durante la cancellazione delle notifiche', 'error');
}

// Reindirizzamento alla pagina delle notifiche
redirect(SITE_URL . '/notifications.php');
?>
