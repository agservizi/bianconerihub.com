<?php
/**
 * Inizializzazione delle tabelle del database
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione del database
require_once 'database.php';

// Tabella degli utenti
$usersTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'default_profile.jpg',
    bio TEXT,
    location VARCHAR(100),
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    is_admin TINYINT(1) DEFAULT 0,
    account_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    verification_code VARCHAR(255),
    is_verified TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella dei post
$postsTable = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    media VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella dei commenti
$commentsTable = "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella dei like
$likesTable = "CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY post_user_unique (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella delle amicizie/followers
$friendshipsTable = "CREATE TABLE IF NOT EXISTS friendships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY follower_following_unique (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella delle notifiche
$notificationsTable = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender_id INT NOT NULL,
    type ENUM('like', 'comment', 'follow', 'mention', 'system') NOT NULL,
    content TEXT NOT NULL,
    reference_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella dei messaggi privati
$messagesTable = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella degli eventi (partite, incontri di tifosi, ecc.)
$eventsTable = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT 'event_default.jpg',
    start_date DATETIME NOT NULL,
    end_date DATETIME DEFAULT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    visibility ENUM('public', 'followers', 'private') DEFAULT 'public',
    capacity INT DEFAULT NULL,
    category VARCHAR(50) DEFAULT 'other',
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella delle partecipazioni agli eventi
$eventParticipantsTable = "CREATE TABLE IF NOT EXISTS event_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('going', 'interested', 'not_going') DEFAULT 'going',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY event_user_unique (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella dei commenti agli eventi
$eventCommentsTable = "CREATE TABLE IF NOT EXISTS event_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabella delle notizie sulla Juventus
$newsTable = "CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'news_default.jpg',
    author_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    views_count INT DEFAULT 0,
    category VARCHAR(50) DEFAULT 'general',
    is_featured TINYINT(1) DEFAULT 0,
    status ENUM('published', 'draft') DEFAULT 'published',
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Creiamo le tabelle
$tables = [
    'users' => $usersTable,
    'posts' => $postsTable,
    'comments' => $commentsTable,
    'likes' => $likesTable,
    'friendships' => $friendshipsTable,
    'notifications' => $notificationsTable,
    'messages' => $messagesTable,
    'events' => $eventsTable,
    'event_participants' => $eventParticipantsTable,
    'event_comments' => $eventCommentsTable,
    'news' => $newsTable
];

// Eseguiamo le query per creare le tabelle
foreach ($tables as $tableName => $tableQuery) {
    if ($conn->query($tableQuery) === TRUE) {
        echo "Tabella {$tableName} creata con successo o già esistente<br>";
    } else {
        echo "Errore nella creazione della tabella {$tableName}: " . $conn->error . "<br>";
    }
}

// Creiamo l'utente admin di default se non esiste
$adminCheck = $conn->query("SELECT * FROM users WHERE username = 'admin'");
if ($adminCheck->num_rows == 0) {
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $adminQuery = "INSERT INTO users (username, email, password, full_name, is_admin, is_verified) 
                   VALUES ('admin', 'admin@bianconerihub.com', '{$adminPassword}', 'Amministratore', 1, 1)";
    
    if ($conn->query($adminQuery) === TRUE) {
        echo "Utente admin creato con successo<br>";
    } else {
        echo "Errore nella creazione dell'utente admin: " . $conn->error . "<br>";
    }
}

echo "Inizializzazione del database completata";
?>
