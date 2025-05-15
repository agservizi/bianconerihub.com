<?php
/**
 * Script di aggiornamento del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

require_once 'database.php';

// Array delle query di aggiornamento
$updates = [
    // Aggiungi colonna verification_code se non esiste
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_code VARCHAR(255) AFTER account_status",
    
    // Aggiungi colonna is_verified se non esiste
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0 AFTER verification_code",
    
    // Aggiungi colonna last_login se non esiste
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME AFTER registration_date",
    
    // Aggiungi colonna registration_date se non esiste
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_date DATETIME DEFAULT CURRENT_TIMESTAMP AFTER location"
];

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Aggiornamento database BiancoNeriHub</h2>";

$success = true;
foreach ($updates as $sql) {
    try {
        if ($conn->query($sql)) {
            echo "<p style='color: green'>✓ Query eseguita con successo: " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p style='color: red'>✗ Errore nell'esecuzione della query: " . htmlspecialchars($sql) . "</p>";
            echo "<p>Errore: " . $conn->error . "</p>";
            $success = false;
        }
    } catch (Exception $e) {
        if (strpos($e->getMessage(), "Duplicate column name") !== false) {
            echo "<p style='color: blue'>ℹ La colonna esiste già: " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p style='color: red'>✗ Errore: " . htmlspecialchars($e->getMessage()) . "</p>";
            $success = false;
        }
    }
}

if ($success) {
    echo "<h3 style='color: green'>Aggiornamento completato con successo!</h3>";
} else {
    echo "<h3 style='color: red'>Si sono verificati degli errori durante l'aggiornamento.</h3>";
}

echo "<hr><a href='../index.php'>Torna al sito</a>";
?>
