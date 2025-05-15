<?php
/**
 * API per verificare la disponibilità di un nome utente
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifichiamo se abbiamo ricevuto il nome utente
if (!isset($_POST['username']) || empty($_POST['username'])) {
    echo json_encode(['available' => false, 'message' => 'Nome utente non specificato']);
    exit;
}

// Otteniamo il nome utente
$username = sanitizeInput($_POST['username']);

// Verifichiamo se rispetta i requisiti
if (strlen($username) < 3 || strlen($username) > 20) {
    echo json_encode(['available' => false, 'message' => 'Il nome utente deve avere tra 3 e 20 caratteri']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    echo json_encode(['available' => false, 'message' => 'Il nome utente può contenere solo lettere, numeri e underscore']);
    exit;
}

// Verifichiamo se il nome utente esiste già
$query = "SELECT id FROM users WHERE username = '{$username}'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo json_encode(['available' => false, 'message' => 'Nome utente già in uso']);
} else {
    echo json_encode(['available' => true, 'message' => 'Nome utente disponibile']);
}
?>
