<?php
/**
 * Configurazione del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Debug temporaneo
error_log("Caricamento configurazione database...");
error_log("Directory corrente: " . __DIR__);
error_log("File .env esiste: " . (file_exists(__DIR__ . '/../.env') ? 'Sì' : 'No'));

// Includiamo il gestore delle variabili d'ambiente
require_once __DIR__ . '/env.php';

// Funzione per debug (da rimuovere dopo il debug)
function debugEnv() {
    $debug = [
        'DB_HOST' => env('DB_HOST', 'non impostato'),
        'DB_USER' => env('DB_USER', 'non impostato'),
        'DB_NAME' => env('DB_NAME', 'non impostato'),
        'ENV_FILE_EXISTS' => file_exists(__DIR__ . '/../.env') ? 'Sì' : 'No',
        'ENV_FILE_PATH' => realpath(__DIR__ . '/../.env'),
        'ENV_FILE_READABLE' => is_readable(__DIR__ . '/../.env') ? 'Sì' : 'No',
        'ENV_FILE_PERMS' => file_exists(__DIR__ . '/../.env') ? substr(sprintf('%o', fileperms(__DIR__ . '/../.env')), -4) : 'N/A',
        'SERVER_VARS' => isset($_SERVER['DB_HOST']) ? 'Impostato' : 'Non impostato',
        'GETENV_DB_HOST' => getenv('DB_HOST'),
        'CURRENT_DIR' => __DIR__,
        'PHP_ERROR_LOG' => error_get_last() ? json_encode(error_get_last()) : 'Nessun errore'
    ];
    return $debug;
}

// DEBUG: Mostra le variabili d'ambiente lette
if (isset($_GET['debug_env'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'DB_HOST' => env('DB_HOST', 'non impostato'),
        'DB_USER' => env('DB_USER', 'non impostato'),
        'DB_PASS' => env('DB_PASS', 'non impostato'),
        'DB_NAME' => env('DB_NAME', 'non impostato'),
        'ENV_FILE_EXISTS' => file_exists(__DIR__ . '/../.env') ? 'Sì' : 'No',
        'SERVER_VARS' => isset($_SERVER['DB_HOST']) ? 'Impostato' : 'Non impostato'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Debug - Uncomment per testare
// die(json_encode(debugEnv()));

// Debug temporaneo
error_log("Caricamento configurazione database...");
$envFile = __DIR__ . '/../.env';
error_log("Cercando file .env in: " . $envFile);
error_log("File .env esiste: " . (file_exists($envFile) ? 'Sì' : 'No'));

// Otteniamo le credenziali del database dalle variabili d'ambiente
$dbHost = env('DB_HOST', null);
$dbUser = env('DB_USER', null);
$dbPass = env('DB_PASS', null);
$dbName = env('DB_NAME', null);

// Verifica che tutte le variabili necessarie siano impostate
if ($dbHost === null || $dbUser === null || $dbPass === null || $dbName === null) {
    error_log("ERRORE: Credenziali database mancanti!");
    error_log("DB_HOST: " . ($dbHost ?? 'NULL'));
    error_log("DB_USER: " . ($dbUser ?? 'NULL'));
    error_log("DB_NAME: " . ($dbName ?? 'NULL'));
    die("Errore di configurazione del database. Contattare l'amministratore del sito.");
}

// Debug - log delle variabili ottenute
error_log("Credenziali database caricate con successo");
error_log("DB_HOST: " . $dbHost);

// Inizializziamo la connessione direttamente al database specifico
try {
    // Disabilita temporaneamente i report di errore per intercettare meglio gli errori
    mysqli_report(MYSQLI_REPORT_OFF);
    
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    // Riattiva i report standard
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Imposta il set di caratteri per la connessione
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Errore di connessione al database: " . $e->getMessage());
    // Per debug (rimuovere in produzione)
    // die("Errore: " . $e->getMessage());
    die("Impossibile connettersi al database. Contattare l'amministratore del sito.");
}

// Verifichiamo la connessione
if ($conn->connect_error) {
    // Logghiamo l'errore ma non mostriamo i dettagli sensibili
    error_log("Errore di connessione al database: " . $conn->connect_error);
    die("Impossibile connettersi al database. Contattare l'amministratore del sito.");
}

// Non è più necessario selezionare il database poiché lo facciamo già nella connessione

/**
 * Funzione per eseguire query al database
 * 
 * @param string $query Query SQL da eseguire
 * @return mixed Risultato della query
 */
function executeQuery($query) {
    global $conn;
    $result = $conn->query($query);
    if (!$result) {
        // In ambiente di produzione, loghiamo l'errore invece di mostrarlo
        $error_message = "Errore nella query: " . $conn->error;
        error_log($error_message);
        
        // Mostriamo un messaggio generico all'utente
        die("Si è verificato un errore nell'esecuzione della query. L'errore è stato registrato.");
    }
    return $result;
}

/**
 * Funzione per sanitizzare gli input
 * 
 * @param string $data Dato da sanitizzare
 * @return string Dato sanitizzato
 */
function sanitizeInput($data) {
    global $conn;
    if (is_null($data)) {
        return '';
    }
    return $conn->real_escape_string(trim($data));
}
?>
