<?php
/**
 * API per la ricerca degli utenti
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit;
}

// Verifichiamo che sia una richiesta GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

// Otteniamo i parametri di ricerca
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Verifichiamo che la query non sia vuota
if (empty($query)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Query di ricerca mancante']);
    exit;
}

try {
    // Prepariamo la query di ricerca
    $searchQuery = '%' . $query . '%';
    
    // Contiamo il totale dei risultati
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM users 
        WHERE (username LIKE ? OR display_name LIKE ?)
        AND id != ?
        AND status = 'active'
    ");
    $stmt->bind_param("ssi", $searchQuery, $searchQuery, $_SESSION['user_id']);
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    
    // Otteniamo gli utenti
    $stmt = $conn->prepare("
        SELECT id, username, display_name, profile_pic,
               (SELECT COUNT(*) FROM followers WHERE followed_id = users.id) as followers_count,
               (SELECT COUNT(*) FROM followers WHERE follower_id = users.id) as following_count,
               EXISTS(SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = users.id) as is_following
        FROM users 
        WHERE (username LIKE ? OR display_name LIKE ?)
        AND id != ?
        AND status = 'active'
        ORDER BY 
            CASE 
                WHEN username LIKE ? THEN 1
                WHEN display_name LIKE ? THEN 2
                ELSE 3
            END,
            followers_count DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bind_param("issiisii", 
        $_SESSION['user_id'],
        $searchQuery, 
        $searchQuery,
        $_SESSION['user_id'],
        $query,
        $query,
        $perPage,
        $offset
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($user = $result->fetch_assoc()) {
        $users[] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'display_name' => $user['display_name'],
            'profile_pic' => $user['profile_pic'],
            'followers_count' => $user['followers_count'],
            'following_count' => $user['following_count'],
            'is_following' => (bool)$user['is_following'],
            'profile_url' => SITE_URL . '/profile.php?username=' . $user['username']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Errore durante la ricerca degli utenti'
    ]);
}
?>
