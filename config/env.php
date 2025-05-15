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
    // Se non è specificato un percorso, usa la directory radice del progetto
    if (is_null($envPath)) {
        $envPath = __DIR__ . '/../.env';
    }
    
    // Verifica se il file .env esiste
    if (!file_exists($envPath)) {
        error_log("File .env non trovato in: " . $envPath);
        return false;
    }
    
    // Leggi il file .env
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        error_log("Impossibile leggere il file .env: " . $envPath);
        return false;
    }
    
    // Elabora ogni riga
    foreach ($lines as $line) {
        // Ignora i commenti
        if (strpos(trim($line), '#') === 0 || strpos(trim($line), '//') === 0) {
            continue;
        }
        
        // Dividi la riga in chiave e valore
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Rimuovi eventuali virgolette attorno al valore
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        // Aggiungi la variabile all'ambiente
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
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
    $value = getenv($key);
    
    if ($value === false) {
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
