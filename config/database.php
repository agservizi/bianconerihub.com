<?php
/**
 * Configurazione del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo il gestore delle variabili d'ambiente
require_once __DIR__ . '/env.php';

// Otteniamo le credenziali del database dalle variabili d'ambiente
$dbHost = env('DB_HOST', 'localhost');
$dbUser = env('DB_USER', 'root');
$dbPass = env('DB_PASS', '');
$dbName = env('DB_NAME', 'bianconerihub');

// Inizializziamo la connessione direttamente al database specifico
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    // Imposta il set di caratteri per la connessione
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Errore di connessione al database: " . $e->getMessage());
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
