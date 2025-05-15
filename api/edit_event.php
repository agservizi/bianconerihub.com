<?php
/**
 * API per la modifica degli eventi
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit;
}

// Verifichiamo che sia una richiesta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

// Otteniamo i dati dalla richiesta
$eventId = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$startDate = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
$endDate = isset($_POST['end_date']) ? trim($_POST['end_date']) : null;

// Validazione base
if (!$eventId || empty($title) || empty($description) || empty($location) || empty($startDate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
    exit;
}

try {
    // Verifichiamo che l'evento esista e che l'utente sia il proprietario
    $stmt = $conn->prepare("SELECT user_id FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Evento non trovato');
    }
    
    $event = $result->fetch_assoc();
    if ($event['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
        throw new Exception('Non hai i permessi per modificare questo evento');
    }
    
    // Aggiorniamo l'evento
    $stmt = $conn->prepare("
        UPDATE events 
        SET title = ?, description = ?, location = ?, 
            start_date = ?, end_date = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->bind_param("sssssi", $title, $description, $location, $startDate, $endDate, $eventId);
    
    if (!$stmt->execute()) {
        throw new Exception('Errore durante l\'aggiornamento dell\'evento');
    }
    
    // Se è stata caricata una nuova immagine
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            throw new Exception('Tipo di file non supportato');
        }
        
        if ($_FILES['image']['size'] > $maxSize) {
            throw new Exception('Il file è troppo grande (max 5MB)');
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'event_' . $eventId . '_' . uniqid() . '.' . $extension;
        $uploadPath = __DIR__ . '/../uploads/events/' . $newFileName;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            throw new Exception('Errore durante il caricamento dell\'immagine');
        }
        
        // Aggiorniamo il nome dell'immagine nel database
        $stmt = $conn->prepare("UPDATE events SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $newFileName, $eventId);
        $stmt->execute();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Evento aggiornato con successo',
        'event_id' => $eventId
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
