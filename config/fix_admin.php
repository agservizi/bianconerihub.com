<?php
/**
 * Script per correggere l'account amministratore
 * BiancoNeriHub - Social network per tifosi della Juventus
 * Data: 15 maggio 2025
 */

// Includiamo la configurazione del database
require_once 'config.php';

// Parametri per l'amministratore
$adminUsername = 'admin';
$adminEmail = 'admin@bianconerihub.com';
$adminPassword = 'admin123'; // Password predefinita, da cambiare dopo il primo accesso
$adminName = 'Amministratore BiancoNeriHub';

// Verifichiamo se lo script è stato eseguito in modalità CLI o browser
$isCli = (php_sapi_name() === 'cli');

/**
 * Funzione per stampare un messaggio formattato
 */
function printMessage($message, $type = 'info') {
    global $isCli;
    
    if ($isCli) {
        switch ($type) {
            case 'success':
                echo "\033[32m[SUCCESSO]\033[0m $message\n";
                break;
            case 'error':
                echo "\033[31m[ERRORE]\033[0m $message\n";
                break;
            case 'warning':
                echo "\033[33m[AVVISO]\033[0m $message\n";
                break;
            default:
                echo "\033[36m[INFO]\033[0m $message\n";
        }
    } else {
        switch ($type) {
            case 'success':
                echo '<div style="color: #28a745; margin: 5px 0;"><strong>[SUCCESSO]</strong> ' . $message . '</div>';
                break;
            case 'error':
                echo '<div style="color: #dc3545; margin: 5px 0;"><strong>[ERRORE]</strong> ' . $message . '</div>';
                break;
            case 'warning':
                echo '<div style="color: #ffc107; margin: 5px 0;"><strong>[AVVISO]</strong> ' . $message . '</div>';
                break;
            default:
                echo '<div style="color: #17a2b8; margin: 5px 0;"><strong>[INFO]</strong> ' . $message . '</div>';
        }
    }
}

// Se eseguito da browser, impostiamo l'output HTML
if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Correzione Account Amministratore - BiancoNeriHub</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                margin: 20px;
                background-color: #f8f9fa;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h1 {
                color: #000;
                border-bottom: 2px solid #c5a47e;
                padding-bottom: 10px;
            }
            .footer {
                margin-top: 30px;
                border-top: 1px solid #eee;
                padding-top: 10px;
                color: #6c757d;
                font-size: 0.9em;
            }
            .btn {
                display: inline-block;
                padding: 8px 16px;
                background-color: #000;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 15px;
            }
            .btn:hover {
                background-color: #333;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Correzione Account Amministratore</h1>
            <div class="results">
    ';
}

printMessage('Inizializzazione dello script di correzione dell\'account amministratore...');

try {
    // Verifichiamo se l'utente admin esiste
    $stmt = $conn->prepare("SELECT id, username, email, password, is_admin, is_verified, account_status FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $adminUsername, $adminEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Se non esiste, lo creiamo
    if ($result->num_rows === 0) {
        printMessage("Account admin non trovato. Creazione in corso...");
        
        // Hash della password
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        // Creazione dell'admin
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, is_admin, is_verified, account_status) VALUES (?, ?, ?, ?, 1, 1, 'active')");
        $stmt->bind_param("ssss", $adminUsername, $adminEmail, $hashedPassword, $adminName);
        
        if ($stmt->execute()) {
            $adminId = $conn->insert_id;
            printMessage("Account admin creato con successo (ID: $adminId).", 'success');
        } else {
            throw new Exception("Impossibile creare l'account admin: " . $stmt->error);
        }
    } else {
        // L'account esiste, verifichiamo i privilegi di amministratore
        $admin = $result->fetch_assoc();
        
        printMessage("Account admin trovato (ID: {$admin['id']}).");
        
        $needsUpdate = false;
        $updates = [];
        
        // Verifichiamo se è impostato come amministratore
        if ((int)$admin['is_admin'] !== 1) {
            $needsUpdate = true;
            $updates[] = "is_admin = 1";
            printMessage("L'account non ha privilegi di amministratore. Correzione in corso...", 'warning');
        }
        
        // Verifichiamo se è verificato
        if ((int)$admin['is_verified'] !== 1) {
            $needsUpdate = true;
            $updates[] = "is_verified = 1";
            printMessage("L'account non è verificato. Correzione in corso...", 'warning');
        }
        
        // Verifichiamo se è attivo
        if ($admin['account_status'] !== 'active') {
            $needsUpdate = true;
            $updates[] = "account_status = 'active'";
            printMessage("L'account non è attivo. Correzione in corso...", 'warning');
        }
        
        // Se servono aggiornamenti, li eseguiamo
        if ($needsUpdate) {
            $updateQuery = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("i", $admin['id']);
            
            if ($stmt->execute()) {
                printMessage("Account admin aggiornato con successo.", 'success');
            } else {
                throw new Exception("Impossibile aggiornare l'account admin: " . $stmt->error);
            }
        } else {
            printMessage("L'account admin è già configurato correttamente.", 'success');
        }
        
        // Opzione per reimpostare la password se richiesto
        if (isset($_GET['reset_password']) && $_GET['reset_password'] === '1') {
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $admin['id']);
            
            if ($stmt->execute()) {
                printMessage("Password dell'admin reimpostata con successo a '$adminPassword'.", 'success');
            } else {
                throw new Exception("Impossibile reimpostare la password admin: " . $stmt->error);
            }
        }
    }
    
    // Verifichiamo la presenza della colonna is_admin nella tabella users
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    if ($result->num_rows === 0) {
        printMessage("La colonna 'is_admin' non esiste nella tabella users. Aggiunta in corso...", 'warning');
        
        if ($conn->query("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER last_login")) {
            printMessage("Colonna 'is_admin' aggiunta con successo alla tabella users.", 'success');
            
            // Impostiamo l'utente admin come amministratore
            $stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE username = ?");
            $stmt->bind_param("s", $adminUsername);
            
            if ($stmt->execute()) {
                printMessage("Privilegi di amministratore assegnati all'utente admin.", 'success');
            } else {
                throw new Exception("Impossibile assegnare privilegi di amministratore: " . $stmt->error);
            }
        } else {
            throw new Exception("Impossibile aggiungere la colonna 'is_admin': " . $conn->error);
        }
    } else {
        printMessage("La colonna 'is_admin' esiste correttamente nella tabella users.");
    }
    
    printMessage("Processo di correzione completato con successo.", 'success');
    
} catch (Exception $e) {
    printMessage("Si è verificato un errore: " . $e->getMessage(), 'error');
}

// Chiudiamo la connessione al database
$conn->close();

// Se eseguito da browser, completiamo l'output HTML
if (!$isCli) {
    echo '</div>
            <div class="footer">
                <p>BiancoNeriHub &copy; ' . date('Y') . ' - Tutti i diritti riservati</p>
                <a href="' . SITE_URL . '" class="btn">Torna al sito</a>
            </div>
        </div>
    </body>
    </html>';
}
?>