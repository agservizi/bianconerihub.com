<?php
/**
 * Script per l'aggiornamento del profilo
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per modificare il profilo', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/settings.php');
}

// Otteniamo i dati
$userId = $_SESSION['user_id'];
$fullName = sanitizeInput($_POST['full_name']);
$bio = isset($_POST['bio']) ? sanitizeInput($_POST['bio']) : '';
$location = isset($_POST['location']) ? sanitizeInput($_POST['location']) : '';

// Validazione dati
if (empty($fullName)) {
    setFlashMessage('Il nome completo è obbligatorio', 'error');
    redirect(SITE_URL . '/settings.php');
}

// Otteniamo l'utente corrente
$user = getCurrentUser();
if (!$user) {
    setFlashMessage('Utente non trovato', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se è stata caricata una nuova immagine profilo
$profilePic = $user['profile_pic']; // Valore attuale

if (!empty($_FILES['profile_pic']['name'])) {
    $uploadDir = ROOT_PATH . '/uploads/profile_pics/';
    
    // Verifichiamo se la directory esiste
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Verifichiamo il tipo di file
    $allowedTypes = ALLOWED_IMAGE_TYPES;
    $fileMimeType = mime_content_type($_FILES['profile_pic']['tmp_name']);
    
    if (!in_array($fileMimeType, $allowedTypes)) {
        setFlashMessage('Tipo di file non supportato. Sono consentiti solo file immagine (JPG, PNG, GIF)', 'error');
        redirect(SITE_URL . '/settings.php');
    }
    
    // Verifichiamo la dimensione del file
    if ($_FILES['profile_pic']['size'] > MAX_UPLOAD_SIZE) {
        setFlashMessage('Il file è troppo grande. La dimensione massima consentita è ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . ' MB', 'error');
        redirect(SITE_URL . '/settings.php');
    }
    
    // Generiamo un nome file univoco
    $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $profilePic = 'profile_' . $userId . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $profilePic;
    
    // Spostiamo il file
    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
        setFlashMessage('Si è verificato un errore durante il caricamento dell\'immagine', 'error');
        redirect(SITE_URL . '/settings.php');
    }
    
    // Eliminiamo la vecchia immagine se non è quella di default
    if ($user['profile_pic'] != 'default_profile.jpg') {
        $oldPath = $uploadDir . $user['profile_pic'];
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
}

// Aggiorniamo i dati dell'utente
$query = "UPDATE users SET 
          full_name = '{$fullName}', 
          bio = '{$bio}', 
          location = '{$location}', 
          profile_pic = '{$profilePic}'
          WHERE id = {$userId}";

if ($conn->query($query)) {
    setFlashMessage('Profilo aggiornato con successo', 'success');
} else {
    setFlashMessage('Si è verificato un errore durante l\'aggiornamento del profilo: ' . $conn->error, 'error');
}

// Reindirizzamento alla pagina delle impostazioni
redirect(SITE_URL . '/settings.php');
?>
