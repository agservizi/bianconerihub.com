<?php
/**
 * Script per la creazione di un nuovo post
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    // Reindirizzamento alla pagina di login
    setFlashMessage('Devi effettuare l\'accesso per pubblicare un post', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL);
}

// Otteniamo i dati
$userId = $_SESSION['user_id'];
$content = sanitizeInput($_POST['content']);
$mediaFile = null;

// Validazione contenuto
if (empty($content) && empty($_FILES['media']['name'])) {
    setFlashMessage('Il post deve contenere testo o un\'immagine', 'error');
    redirect(SITE_URL);
}

// Gestione caricamento media
if (!empty($_FILES['media']['name'])) {
    $uploadDir = ROOT_PATH . '/uploads/posts/';
    
    // Verifichiamo se la directory esiste
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Verifichiamo il tipo di file
    $allowedTypes = ALLOWED_IMAGE_TYPES;
    $fileMimeType = mime_content_type($_FILES['media']['tmp_name']);
    
    if (!in_array($fileMimeType, $allowedTypes)) {
        setFlashMessage('Tipo di file non supportato. Sono consentiti solo file immagine (JPG, PNG, GIF)', 'error');
        redirect(SITE_URL);
    }
    
    // Verifichiamo la dimensione del file
    if ($_FILES['media']['size'] > MAX_UPLOAD_SIZE) {
        setFlashMessage('Il file è troppo grande. La dimensione massima consentita è ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . ' MB', 'error');
        redirect(SITE_URL);
    }
    
    // Generiamo un nome file univoco
    $extension = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
    $mediaFile = 'post_' . $userId . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $mediaFile;
    
    // Spostiamo il file
    if (!move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
        setFlashMessage('Si è verificato un errore durante il caricamento dell\'immagine', 'error');
        redirect(SITE_URL);
    }
}

// Inseriamo il post nel database
$mediaValue = $mediaFile ? "'{$mediaFile}'" : "NULL";
$query = "INSERT INTO posts (user_id, content, media) VALUES ({$userId}, '{$content}', {$mediaValue})";

if ($conn->query($query)) {
    // Post creato con successo
    setFlashMessage('Post pubblicato con successo', 'success');
    
    // Processiamo eventuali menzioni nel post
    preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $mentions);
    if (!empty($mentions[1])) {
        $postId = $conn->insert_id;
        $currentUser = getCurrentUser();
        
        foreach ($mentions[1] as $mentionedUsername) {
            // Otteniamo l'ID dell'utente menzionato
            $mentionedQuery = $conn->query("SELECT id FROM users WHERE username = '{$mentionedUsername}'");
            
            if ($mentionedQuery->num_rows > 0) {
                $mentioned = $mentionedQuery->fetch_assoc();
                $mentionedId = $mentioned['id'];
                
                // Verifichiamo che non sia l'utente stesso
                if ($mentionedId != $userId) {
                    $notificationContent = "@{$currentUser['username']} ti ha menzionato in un post";
                    
                    $conn->query("INSERT INTO notifications (user_id, sender_id, type, content, reference_id) 
                                 VALUES ({$mentionedId}, {$userId}, 'mention', '{$notificationContent}', {$postId})");
                }
            }
        }
    }
} else {
    setFlashMessage('Si è verificato un errore durante la pubblicazione del post: ' . $conn->error, 'error');
}

// Reindirizzamento alla home page
redirect(SITE_URL);
?>
