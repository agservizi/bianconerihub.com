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
    // Se non è specificato un percorso, cerca nella root del progetto
    if ($envPath === null) {
        $envPath = __DIR__ . '/../.env';
    }
    
    $realPath = realpath($envPath);
    
    // Debug - Log del percorso e dei permessi
    error_log("Tentativo di caricamento .env da: " . $envPath);
    error_log("Percorso reale: " . ($realPath ? $realPath : "non trovato"));
    error_log("Il file esiste? " . (file_exists($envPath) ? "Sì" : "No"));
    error_log("Permessi file: " . (file_exists($envPath) ? decoct(fileperms($envPath) & 0777) : "N/A"));
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
