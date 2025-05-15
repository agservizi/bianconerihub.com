<?php
/**
 * API per ottenere i messaggi
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

// Otteniamo i parametri
$conversationId = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

try {
    // Verifichiamo che l'utente faccia parte della conversazione
    $stmt = $conn->prepare("
        SELECT c.id, c.user1_id, c.user2_id,
               u1.username as user1_username, u1.display_name as user1_display_name, u1.profile_pic as user1_profile_pic,
               u2.username as user2_username, u2.display_name as user2_display_name, u2.profile_pic as user2_profile_pic
        FROM conversations c
        JOIN users u1 ON c.user1_id = u1.id
        JOIN users u2 ON c.user2_id = u2.id
        WHERE c.id = ? AND (c.user1_id = ? OR c.user2_id = ?)
    ");
    $stmt->bind_param("iii", $conversationId, $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $conversationResult = $stmt->get_result();
    
    if ($conversationResult->num_rows === 0) {
        throw new Exception('Conversazione non trovata');
    }
    
    $conversation = $conversationResult->fetch_assoc();
    
    // Otteniamo il totale dei messaggi
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM messages
        WHERE conversation_id = ?
    ");
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    
    // Otteniamo i messaggi
    $stmt = $conn->prepare("
        SELECT m.*, u.username, u.display_name, u.profile_pic
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $conversationId, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($message = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $message['id'],
            'sender_id' => $message['sender_id'],
            'content' => $message['content'],
            'created_at' => $message['created_at'],
            'sender' => [
                'username' => $message['username'],
                'display_name' => $message['display_name'],
                'profile_pic' => $message['profile_pic']
            ],
            'is_mine' => $message['sender_id'] === $_SESSION['user_id']
        ];
    }
    
    // Marchiamo i messaggi come letti
    if (!empty($messages)) {
        $stmt = $conn->prepare("
            UPDATE messages
            SET read_at = CURRENT_TIMESTAMP
            WHERE conversation_id = ?
            AND sender_id != ?
            AND read_at IS NULL
        ");
        $stmt->bind_param("ii", $conversationId, $_SESSION['user_id']);
        $stmt->execute();
    }
    
    // Prepariamo i dati dell'altro utente
    $otherUser = [
        'id' => $conversation['user1_id'] === $_SESSION['user_id'] ? $conversation['user2_id'] : $conversation['user1_id'],
        'username' => $conversation['user1_id'] === $_SESSION['user_id'] ? $conversation['user2_username'] : $conversation['user1_username'],
        'display_name' => $conversation['user1_id'] === $_SESSION['user_id'] ? $conversation['user2_display_name'] : $conversation['user1_display_name'],
        'profile_pic' => $conversation['user1_id'] === $_SESSION['user_id'] ? $conversation['user2_profile_pic'] : $conversation['user1_profile_pic']
    ];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'conversation' => [
                'id' => $conversation['id'],
                'other_user' => $otherUser
            ],
            'messages' => $messages,
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
        'message' => $e->getMessage()
    ]);
}
?>
