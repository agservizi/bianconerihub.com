<?php
/**
 * API per annullare un evento
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

// Verifichiamo se abbiamo ricevuto l'ID dell'evento
if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID evento mancante']);
    exit;
}

// Otteniamo i dati
$eventId = intval($_POST['event_id']);
$userId = $_SESSION['user_id'];

// Otteniamo i dettagli dell'evento
$eventQuery = "SELECT * FROM events WHERE id = {$eventId}";
$eventResult = $conn->query($eventQuery);

if ($eventResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Evento non trovato']);
    exit;
}

$event = $eventResult->fetch_assoc();

// Verifichiamo se l'utente ha i permessi per annullare l'evento
if ($event['created_by'] != $userId && !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per annullare questo evento']);
    exit;
}

// Verifichiamo se l'evento è già stato annullato
if ($event['status'] == 'cancelled') {
    echo json_encode(['success' => false, 'message' => 'L\'evento è già stato annullato']);
    exit;
}

// Iniziamo una transazione
$conn->begin_transaction();

try {
    // Aggiorniamo lo stato dell'evento
    $updateEventQuery = "UPDATE events SET status = 'cancelled' WHERE id = {$eventId}";
    $conn->query($updateEventQuery);
    
    // Otteniamo la lista dei partecipanti per inviare le notifiche
    $participantsQuery = "SELECT user_id FROM event_participants 
                         WHERE event_id = {$eventId} AND status IN ('going', 'interested')";
    
    $participantsResult = $conn->query($participantsQuery);
    
    if ($participantsResult->num_rows > 0) {
        // Prepariamo l'inserimento in batch delle notifiche
        $notificationValues = [];
        
        while ($participant = $participantsResult->fetch_assoc()) {
            if ($participant['user_id'] != $userId) { // Non inviamo notifica all'organizzatore se è lui che annulla
                $notificationValues[] = "({$participant['user_id']}, {$userId}, 'event_cancelled', 'ha annullato l\'evento \"{$event['title']}\" a cui stavi partecipando', {$eventId}, NOW())";
            }
        }
        
        if (!empty($notificationValues)) {
            $insertNotificationsQuery = "INSERT INTO notifications (user_id, sender_id, type, content, reference_id, created_at) VALUES " . implode(', ', $notificationValues);
            $conn->query($insertNotificationsQuery);
        }
    }
    
    // Commit della transazione
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Evento annullato con successo']);
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    echo json_encode(['success' => false, 'message' => 'Errore nell\'annullamento dell\'evento: ' . $e->getMessage()]);
}
?>
