<?php
/**
 * Configurazione del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo il file con le credenziali del database
$credentialsFile = __DIR__ . '/db_credentials.php';

// Verifichiamo se il file delle credenziali esiste
if (file_exists($credentialsFile)) {
    require_once $credentialsFile;
} else {
    die("Errore: File di credenziali database non trovato.");
}

// Inizializziamo la connessione direttamente al database specifico
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Imposta il set di caratteri per la connessione
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Errore di connessione al database: " . $e->getMessage());
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
