<?php
/**
 * API per il caricamento di file multimediali
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

// Verifichiamo che sia stato caricato un file
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nessun file caricato']);
    exit;
}

// Impostazioni per il caricamento
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'video/mp4' => 'mp4',
    'video/quicktime' => 'mov'
];
$maxSize = 10 * 1024 * 1024; // 10MB
$uploadType = isset($_POST['type']) ? trim($_POST['type']) : 'post'; // post, message, event

try {
    $file = $_FILES['file'];
    
    // Verifichiamo il tipo di file
    if (!isset($allowedTypes[$file['type']])) {
        throw new Exception('Tipo di file non supportato');
    }
    
    // Verifichiamo la dimensione
    if ($file['size'] > $maxSize) {
        throw new Exception('Il file è troppo grande (max 10MB)');
    }
    
    // Determiniamo la cartella di destinazione
    $uploadDir = __DIR__ . '/../uploads/';
    switch ($uploadType) {
        case 'post':
            $uploadDir .= 'posts/';
            break;
        case 'message':
            $uploadDir .= 'messages/';
            break;
        case 'event':
            $uploadDir .= 'events/';
            break;
        default:
            throw new Exception('Tipo di upload non valido');
    }
    
    // Creiamo la cartella se non esiste
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generiamo un nome file univoco
    $extension = $allowedTypes[$file['type']];
    $fileName = uniqid('media_') . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Spostiamo il file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Errore durante il caricamento del file');
    }
    
    // Se è un'immagine, creiamo una thumbnail
    if (strpos($file['type'], 'image/') === 0) {
        // Directory per le thumbnail
        $thumbDir = $uploadDir . 'thumbnails/';
        if (!file_exists($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }
        
        // Creiamo la thumbnail
        $thumbPath = $thumbDir . $fileName;
        $maxThumbSize = 300;
        
        list($width, $height) = getimagesize($filePath);
        $ratio = min($maxThumbSize / $width, $maxThumbSize / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        switch ($file['type']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($filePath);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($filePath);
                break;
            default:
                $source = false;
        }
        
        if ($source) {
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            switch ($file['type']) {
                case 'image/jpeg':
                    imagejpeg($thumb, $thumbPath, 80);
                    break;
                case 'image/png':
                    imagepng($thumb, $thumbPath, 8);
                    break;
                case 'image/gif':
                    imagegif($thumb, $thumbPath);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($thumb);
        }
    }
    
    // Registriamo il file nel database
    $stmt = $conn->prepare("
        INSERT INTO media (user_id, filename, type, upload_type, created_at)
        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
    ");
    $stmt->bind_param("isss", $_SESSION['user_id'], $fileName, $file['type'], $uploadType);
    $stmt->execute();
    $mediaId = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $mediaId,
            'filename' => $fileName,
            'type' => $file['type'],
            'url' => UPLOADS_URL . '/' . $uploadType . '/' . $fileName,
            'thumbnail_url' => strpos($file['type'], 'image/') === 0 
                ? UPLOADS_URL . '/' . $uploadType . '/thumbnails/' . $fileName 
                : null
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
