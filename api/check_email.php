<?php
/**
 * API per verificare la disponibilità di un'email
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifichiamo se abbiamo ricevuto l'email
if (!isset($_POST['email']) || empty($_POST['email'])) {
    echo json_encode(['available' => false, 'message' => 'Email non specificata']);
    exit;
}

// Otteniamo l'email
$email = sanitizeInput($_POST['email']);

// Verifichiamo se è un'email valida
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => 'Email non valida']);
    exit;
}

// Verifichiamo se l'email esiste già
$query = "SELECT id FROM users WHERE email = '{$email}'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo json_encode(['available' => false, 'message' => 'Email già registrata']);
} else {
    echo json_encode(['available' => true, 'message' => 'Email disponibile']);
}
?>
