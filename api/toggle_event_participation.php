<?php
/**
 * API per gestire la partecipazione agli eventi
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

// Verifichiamo se abbiamo ricevuto l'ID dell'evento e lo stato
if (!isset($_POST['event_id']) || empty($_POST['event_id']) || !isset($_POST['status']) || empty($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit;
}

// Otteniamo i dati
$eventId = intval($_POST['event_id']);
$userId = $_SESSION['user_id'];
$status = sanitizeInput($_POST['status']);

// Validazioni
$validStatuses = ['going', 'interested', 'not_going'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Stato non valido']);
    exit;
}

// Verifichiamo se l'evento esiste e se è attivo
$eventQuery = "SELECT e.*, u.username, u.full_name FROM events e 
               JOIN users u ON e.created_by = u.id
               WHERE e.id = {$eventId}";

$eventResult = $conn->query($eventQuery);

if ($eventResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Evento non trovato']);
    exit;
}

$event = $eventResult->fetch_assoc();

// Verifichiamo se l'evento è annullato o passato
if ($event['status'] == 'cancelled') {
    echo json_encode(['success' => false, 'message' => 'L\'evento è stato annullato']);
    exit;
}

if (strtotime($event['start_date']) < time() && (!$event['end_date'] || strtotime($event['end_date']) < time())) {
    echo json_encode(['success' => false, 'message' => 'L\'evento è già terminato']);
    exit;
}

// Verifichiamo se l'evento ha raggiunto la capacità massima (solo per status = 'going')
if ($status == 'going' && $event['capacity']) {
    $participantsQuery = "SELECT COUNT(*) as count FROM event_participants 
                          WHERE event_id = {$eventId} AND status = 'going'";
    
    $participantsResult = $conn->query($participantsQuery);
    $participantsCount = $participantsResult->fetch_assoc()['count'];
    
    if ($participantsCount >= $event['capacity']) {
        echo json_encode(['success' => false, 'message' => 'L\'evento ha raggiunto la capacità massima']);
        exit;
    }
}

// Verifichiamo se l'utente ha già indicato la partecipazione
$participationQuery = "SELECT * FROM event_participants 
                      WHERE event_id = {$eventId} AND user_id = {$userId}";

$participationResult = $conn->query($participationQuery);
$isExistingParticipation = $participationResult->num_rows > 0;
$oldStatus = '';

if ($isExistingParticipation) {
    $participation = $participationResult->fetch_assoc();
    $oldStatus = $participation['status'];
    
    // Se lo stato è uguale, allora lo rimuoviamo (toggle)
    if ($oldStatus == $status) {
        $conn->query("DELETE FROM event_participants 
                     WHERE event_id = {$eventId} AND user_id = {$userId}");
        
        // Aggiorniamo il conteggio dei partecipanti
        $newParticipantsCountQuery = "SELECT COUNT(*) as count FROM event_participants 
                                     WHERE event_id = {$eventId} AND status = 'going'";
        
        $newParticipantsResult = $conn->query($newParticipantsCountQuery);
        $newParticipantsCount = $newParticipantsResult->fetch_assoc()['count'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Partecipazione rimossa', 
            'participants_count' => $newParticipantsCount,
            'reload' => true
        ]);
        exit;
    }
    
    // Altrimenti aggiorniamo lo stato
    $conn->query("UPDATE event_participants 
                 SET status = '{$status}', created_at = NOW() 
                 WHERE event_id = {$eventId} AND user_id = {$userId}");
} else {
    // Creiamo una nuova partecipazione
    $conn->query("INSERT INTO event_participants (event_id, user_id, status, created_at) 
                 VALUES ({$eventId}, {$userId}, '{$status}', NOW())");
}

// Aggiorniamo il conteggio dei partecipanti
$newParticipantsCountQuery = "SELECT COUNT(*) as count FROM event_participants 
                             WHERE event_id = {$eventId} AND status = 'going'";

$newParticipantsResult = $conn->query($newParticipantsCountQuery);
$newParticipantsCount = $newParticipantsResult->fetch_assoc()['count'];

// Creiamo una notifica per l'organizzatore dell'evento (solo per partecipazioni going/interested)
if (($status == 'going' || $status == 'interested') && $event['created_by'] != $userId) {
    $notificationType = $status == 'going' ? 'event_participation' : 'event_interest';
    $notificationContent = $status == 'going' 
        ? "parteciperà all'evento \"{$event['title']}\""
        : "è interessato all'evento \"{$event['title']}\"";
    
    $insertNotificationQuery = "INSERT INTO notifications (user_id, sender_id, type, content, reference_id, created_at) 
                              VALUES ({$event['created_by']}, {$userId}, '{$notificationType}', '{$notificationContent}', {$eventId}, NOW())";
    
    $conn->query($insertNotificationQuery);
}

// Messaggio di conferma
$statusMessages = [
    'going' => 'Hai confermato la tua partecipazione all\'evento',
    'interested' => 'Hai indicato il tuo interesse per l\'evento',
    'not_going' => 'Hai indicato che non parteciperai all\'evento'
];

echo json_encode([
    'success' => true, 
    'message' => $statusMessages[$status], 
    'participants_count' => $newParticipantsCount,
    'reload' => true
]);
?>
