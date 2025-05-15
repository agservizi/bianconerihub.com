<?php
/**
 * Gestione delle variabili d'ambiente
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

/**
 * Carica le variabili d'ambiente dal file .env
 * 
 * @param string $envPath Percorso del file .env
 * @return bool True se il file è stato caricato, false altrimenti
 */
function loadEnv($envPath = null) {
    // Array di possibili percorsi dove cercare il file .env
    $possiblePaths = [
        __DIR__ . '/../.env',                    // Percorso relativo standard
        $_SERVER['DOCUMENT_ROOT'] . '/../.env',  // Un livello sopra la document root
        $_SERVER['DOCUMENT_ROOT'] . '/.env',     // Nella document root
        '/home/u427445037/domains/bianconerihub.com/.env'  // Percorso assoluto sul server
    ];
    
    // Se è stato specificato un percorso, lo aggiungiamo all'inizio dell'array
    if ($envPath !== null) {
        array_unshift($possiblePaths, $envPath);
    }
    
    // Cerca il file .env in tutti i possibili percorsi
    $envPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path) && is_readable($path)) {
            $envPath = $path;
            break;
        }
        error_log("Tentativo fallito per .env in: " . $path);
    }
    
    // Se non troviamo il file da nessuna parte, logghiamo e usciamo
    if ($envPath === null) {
        error_log("File .env non trovato in nessuno dei percorsi controllati");
        return false;
    }
    
    error_log("File .env trovato in: " . $envPath);
    error_log("File leggibile? " . (is_readable($envPath) ? "Sì" : "No"));
    
    if (!file_exists($envPath)) {
        error_log('File .env non trovato in: ' . $envPath);
        return false;
    }
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        error_log('Impossibile leggere il file .env: ' . $envPath);
        return false;
    }
    foreach ($lines as $line) {
        $line = trim($line);        // Ignora linee vuote e commenti
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || substr($line, 0, 2) === '//') {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
    return true;
}

/**
 * Ottieni il valore di una variabile d'ambiente
 * 
 * @param string $key Nome della variabile
 * @param mixed $default Valore predefinito se la variabile non esiste
 * @return mixed Valore della variabile o valore predefinito
 */
function env($key, $default = null) {
    // Prima controlla nelle variabili d'ambiente PHP
    $value = getenv($key);
    
    // Se non trovato, cerca in $_ENV e $_SERVER
    if ($value === false) {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : false);
    }
    
    // Se ancora non trovato, ritorna il default
    if ($value === false) {
        error_log("Variabile d'ambiente non trovata: " . $key);
        return $default;
    }
    
    // Converti alcuni valori specifici
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
        case 'empty':
        case '(empty)':
            return '';
    }
    
    return $value;
}

// Carica le variabili d'ambiente
loadEnv();
?>
