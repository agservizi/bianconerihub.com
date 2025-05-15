<?php
/**
 * Gestione dell'aggiornamento dell'aspetto del profilo
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Devi effettuare l\'accesso per modificare il profilo']);
    exit;
}

// Verifichiamo che sia una richiesta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Gestiamo l'upload dell'immagine del profilo
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $file = $_FILES['profile_pic'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Verifichiamo il tipo di file
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo di file non supportato. Sono ammessi solo file JPEG, PNG e GIF.');
        }
        
        // Verifichiamo la dimensione
        if ($file['size'] > $maxSize) {
            throw new Exception('Il file è troppo grande. La dimensione massima consentita è 5MB.');
        }
        
        // Generiamo un nome univoco per il file
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../uploads/profile_pics/' . $newFileName;
        
        // Spostiamo il file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Errore durante il caricamento del file.');
        }
        
        // Aggiorniamo il database
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $newFileName, $userId);
        
        if (!$stmt->execute()) {
            // Se l'aggiornamento fallisce, eliminiamo il file caricato
            unlink($uploadPath);
            throw new Exception('Errore durante l\'aggiornamento del profilo.');
        }
        
        // Se c'era un'immagine precedente (non default), la eliminiamo
        $oldPicQuery = "SELECT profile_pic FROM users WHERE id = {$userId}";
        $oldPicResult = $conn->query($oldPicQuery);
        if ($oldPicResult && $oldPicResult->num_rows > 0) {
            $oldPic = $oldPicResult->fetch_assoc()['profile_pic'];
            if ($oldPic !== 'logo.png') {
                $oldPicPath = __DIR__ . '/../uploads/profile_pics/' . $oldPic;
                if (file_exists($oldPicPath)) {
                    unlink($oldPicPath);
                }
            }
        }
    }
    
    // Aggiorniamo la bio se fornita
    if (isset($_POST['bio'])) {
        $bio = trim($_POST['bio']);
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->bind_param("si", $bio, $userId);
        $stmt->execute();
    }
    
    // Aggiorniamo la location se fornita
    if (isset($_POST['location'])) {
        $location = trim($_POST['location']);
        $stmt = $conn->prepare("UPDATE users SET location = ? WHERE id = ?");
        $stmt->bind_param("si", $location, $userId);
        $stmt->execute();
    }
    
    $response['success'] = true;
    $response['message'] = 'Profilo aggiornato con successo!';
    
    if (isset($newFileName)) {
        $response['profile_pic'] = $newFileName;
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Inviamo la risposta
header('Content-Type: application/json');
echo json_encode($response);
?>
