<?php
/**
 * Configurazione principale
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo il gestore delle variabili d'ambiente, se non è già stato incluso
if (!function_exists('env')) {
    require_once __DIR__ . '/env.php';
}

// Modalità ambiente
$appEnv = env('APP_ENV', 'production');
$isProduction = ($appEnv === 'production');

// Gestione errori in base all'ambiente
if ($isProduction) {
    // Disabilitiamo la visualizzazione degli errori in produzione
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
} else {
    // Abilitiamo la visualizzazione degli errori in sviluppo
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
error_reporting(E_ALL);

// DEBUG: Abilita la visualizzazione degli errori per il debug temporaneo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Impostazione del fuso orario
date_default_timezone_set('Europe/Rome');

// Definizione delle costanti del sito
define('SITE_NAME', 'BiancoNeriHub');
define('SITE_URL', $isProduction ? 'https://bianconerihub.com' : 'http://localhost/bianconerihub');
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', SITE_URL . '/uploads');

// Percorsi assoluti
define('ROOT_PATH', __DIR__ . '/..');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Impostazioni email
define('EMAIL_FROM', env('EMAIL_FROM', 'noreply@bianconerihub.com'));
define('EMAIL_NAME', env('EMAIL_NAME', 'BiancoNeriHub'));

// Impostazioni di sicurezza
define('SECURE_SESSION', $isProduction); // Abilitato in produzione (richiede HTTPS)
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 86400)); // 24 ore in secondi
define('SALT', env('SALT', 'bianconeri_since_1897')); // Salt per funzioni di hash

// Limitazioni
define('MAX_UPLOAD_SIZE', env('UPLOAD_MAX_SIZE', 5 * 1024 * 1024)); // 5MB di default
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('POSTS_PER_PAGE', env('POSTS_PER_PAGE', 10));
define('COMMENTS_PER_PAGE', env('COMMENTS_PER_PAGE', 5));

// Caricamento delle dipendenze
require_once ROOT_PATH . '/config/database.php';
session_start();

/**
 * Funzione per il caricamento automatico delle classi
 * 
 * @param string $className Nome della classe da caricare
 * @return void
 */
function autoload($className) {
    $classFile = ROOT_PATH . '/includes/classes/' . $className . '.php';
    
    if (file_exists($classFile)) {
        require_once $classFile;
    }
}

// Registra la funzione di autoload
spl_autoload_register('autoload');

/**
 * Funzione per reindirizzare a una pagina
 * 
 * @param string $location URL di destinazione
 * @return void
 */
function redirect($location) {
    header("Location: {$location}");
    exit;
}

/**
 * Funzione per verificare se l'utente è autenticato
 * 
 * @return bool True se l'utente è autenticato, false altrimenti
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Funzione per verificare se l'utente è un amministratore
 * 
 * @return bool True se l'utente è un amministratore, false altrimenti
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

/**
 * Funzione per ottenere i dati dell'utente corrente
 * 
 * @return array|null Dati dell'utente o null se non autenticato
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        global $conn;
        $userId = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = {$userId}";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    
    return null;
}

/**
 * Funzione per formattare le date
 * 
 * @param string $date Data da formattare
 * @param string $format Formato della data
 * @return string Data formattata
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Funzione per mostrare messaggi di notifica
 * 
 * @param string $message Messaggio da mostrare
 * @param string $type Tipo di messaggio (success, error, warning, info)
 * @return void
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Funzione per ottenere e rimuovere i messaggi di notifica
 * 
 * @return array|null Messaggio di notifica o null se non presente
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Funzione per troncare un testo
 * 
 * @param string $text Testo da troncare
 * @param int $length Lunghezza massima
 * @param string $append Testo da aggiungere alla fine
 * @return string Testo troncato
 */
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= $append;
    }
    
    return $text;
}
?>
