<?php
/**
 * Pagina di logout
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once 'config/config.php';

// Verifichiamo se l'utente è autenticato
if (isLoggedIn()) {
    // Eliminiamo il token "Ricordami" se presente
    if (isset($_COOKIE['remember_token'])) {
        $tokenParts = explode(':', $_COOKIE['remember_token']);
        $userId = $tokenParts[0];
        
        // Eliminiamo il token dal database
        $query = "DELETE FROM remember_tokens WHERE user_id = {$userId}";
        $conn->query($query);
        
        // Eliminiamo il cookie
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }
    
    // Distruggiamo la sessione
    session_unset();
    session_destroy();
    
    // Impostiamo un messaggio di notifica
    session_start();
    setFlashMessage("Logout effettuato con successo!", "success");
}

// Reindirizziamo alla pagina di login
redirect(SITE_URL . '/login.php');
?>
