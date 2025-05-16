<?php
/**
 * Script per aggiungere la colonna is_admin alla tabella users
 */

require_once 'config.php';
require_once 'database.php';

try {
    // Leggiamo il contenuto del file SQL
    $sql = file_get_contents(__DIR__ . '/update_admin.sql');
    
    // Eseguiamo la query
    if ($conn->multi_query($sql)) {
        echo "Colonna is_admin aggiunta con successo!\n";
        
        // Impostiamo il primo utente come admin (opzionale)
        $conn->query("UPDATE users SET is_admin = 1 WHERE id = 1");
        echo "Primo utente impostato come admin.\n";
    }
} catch (Exception $e) {
    echo "Errore durante l'aggiornamento del database: " . $e->getMessage() . "\n";
}
