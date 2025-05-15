<?php
/**
 * Header del sito
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once __DIR__ . '/../config/config.php';

// Otteniamo l'utente corrente
$currentUser = getCurrentUser();

// Contatore notifiche non lette
$notificationsCount = 0;
$messagesCount = 0;

if ($currentUser) {
    // Contiamo le notifiche non lette
    $userId = $currentUser['id'];
    $notificationsQuery = "SELECT COUNT(*) as count FROM notifications WHERE user_id = {$userId} AND is_read = 0";
    $notificationsResult = $conn->query($notificationsQuery);
    
    if ($notificationsResult && $notificationsResult->num_rows > 0) {
        $notificationsCount = $notificationsResult->fetch_assoc()['count'];
    }
    
    // Contiamo i messaggi non letti
    $messagesQuery = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = {$userId} AND is_read = 0";
    $messagesResult = $conn->query($messagesQuery);
    
    if ($messagesResult && $messagesResult->num_rows > 0) {
        $messagesCount = $messagesResult->fetch_assoc()['count'];
    }
}

// Otteniamo il titolo della pagina
$pageTitle = isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME;
?>
<!DOCTYPE html>
<html lang="it">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">
    
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome per le icone -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS personalizzato -->
    <link href="<?php echo SITE_URL; ?>/css/style.css" rel="stylesheet">
    
    <?php if (isset($extraCss)): ?>
        <!-- CSS aggiuntivo specifico per la pagina -->
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Logo BiancoNeriHub" height="40" class="me-2">
                <span class="brand-text">BiancoNeriHub</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/events.php">
                            <i class="fas fa-calendar-alt"></i> Eventi
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/notifications.php">
                                <i class="fas fa-bell"></i> Notifiche
                                <?php if ($notificationsCount > 0): ?>
                                    <span class="badge bg-danger"><?php echo $notificationsCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/messages.php">
                                <i class="fas fa-envelope"></i> Messaggi
                                <?php if ($messagesCount > 0): ?>
                                    <span class="badge bg-danger"><?php echo $messagesCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Form di ricerca -->
                <form class="d-flex mx-3" action="<?php echo SITE_URL; ?>/search.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cerca..." required>
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Menu utente -->
                <?php if (isLoggedIn()): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                            <img src="<?php echo UPLOADS_URL . '/profile_pics/' . $currentUser['profile_pic']; ?>" 
                                 alt="<?php echo htmlspecialchars($currentUser['username']); ?>" 
                                 class="rounded-circle" 
                                 width="32" 
                                 height="32">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile.php">
                                    <i class="fas fa-user"></i> Profilo
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/settings.php">
                                    <i class="fas fa-cog"></i> Impostazioni
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Esci
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="nav-item">
                        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light me-2">Accedi</a>
                        <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-light">Registrati</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenitore principale -->
    <div class="container main-container py-4">
        <?php displayFlashMessages(); ?>
