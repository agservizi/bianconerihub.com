<?php
/**
 * Script di aggiornamento del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

require_once 'database.php';

// Array delle query di aggiornamento
$updates = [
    // Aggiungiamo prima le colonne base
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT AFTER profile_pic",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS location VARCHAR(100) AFTER bio",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_date DATETIME DEFAULT CURRENT_TIMESTAMP AFTER location",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME AFTER registration_date",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS account_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER is_admin",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_code VARCHAR(255) AFTER account_status",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0 AFTER verification_code",
    
    // Aggiorniamo i valori di default per le date mancanti
    "UPDATE users SET registration_date = CURRENT_TIMESTAMP WHERE registration_date IS NULL",
    "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE last_login IS NULL"
];

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Aggiornamento database BiancoNeriHub</h2>";
echo "<style>
    .success { color: green; }
    .warning { color: orange; }
    .error { color: red; }
    .info { color: blue; }
</style>";

$hasErrors = false;
foreach ($updates as $sql) {
    try {
        if ($conn->query($sql)) {
            echo "<p class='success'>✓ Query eseguita con successo: " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p class='warning'>⚠ La query non ha prodotto risultati: " . htmlspecialchars($sql) . "</p>";
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        // Ignoriamo gli errori per colonne duplicate o già esistenti
        if (strpos($errorMessage, "Duplicate column") !== false || 
            strpos($errorMessage, "Column already exists") !== false) {
            echo "<p class='info'>ℹ La colonna esiste già: " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p class='error'>✗ Errore: " . htmlspecialchars($errorMessage) . "</p>";
            echo "<p class='error'>Query: " . htmlspecialchars($sql) . "</p>";
            $hasErrors = true;
        }
    }
}

if ($hasErrors) {
    echo "<h3 class='error'>Si sono verificati alcuni errori durante l'aggiornamento.</h3>";
    echo "<p>Controlla i messaggi di errore sopra per i dettagli.</p>";
} else {
    echo "<h3 class='success'>Aggiornamento completato con successo!</h3>";
}

echo "<hr><p><a href='../index.php'>Torna al sito</a></p>";
?>
