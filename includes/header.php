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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>/images/favicon.ico">
    
    <!-- Font Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS personalizzato -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    
    <?php if (isset($extraCss)): ?>
        <!-- CSS aggiuntivo specifico per la pagina -->
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="BiancoNeriHub" height="40">
                BiancoNeriHub
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <?php if ($currentUser): ?>
                    <!-- Barra di ricerca -->
                    <form class="d-flex mx-auto" action="<?php echo SITE_URL; ?>/search.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Cerca utenti, post, notizie...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Menu utente -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/events.php">
                                <i class="fas fa-calendar-alt"></i> Eventi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/news.php">
                                <i class="fas fa-newspaper"></i> News
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/notifications.php">
                                <i class="fas fa-bell"></i> Notifiche
                                <?php if ($notificationsCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $notificationsCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/messages.php">
                                <i class="fas fa-envelope"></i> Messaggi
                                <?php if ($messagesCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $messagesCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $currentUser['profile_pic']; ?>" alt="<?php echo $currentUser['username']; ?>" class="rounded-circle" width="24" height="24">
                                <?php echo $currentUser['username']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $currentUser['id']; ?>">
                                        <i class="fas fa-user"></i> Profilo
                                    </a>
                                </li>
                                <?php if (isAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/">
                                            <i class="fas fa-cog"></i> Amministrazione
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>/settings.php">
                                        <i class="fas fa-cog"></i> Impostazioni
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Menu visitatore -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/login.php">
                                <i class="fas fa-sign-in-alt"></i> Accedi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/register.php">
                                <i class="fas fa-user-plus"></i> Registrati
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Contenitore principale -->
    <div class="container main-container">
        <!-- Messaggio flash -->
        <?php
        $flashMessage = getFlashMessage();
        if ($flashMessage):
            $alertClass = 'alert-info';
            
            switch ($flashMessage['type']) {
                case 'success':
                    $alertClass = 'alert-success';
                    break;
                case 'error':
                    $alertClass = 'alert-danger';
                    break;
                case 'warning':
                    $alertClass = 'alert-warning';
                    break;
            }
        ?>
            <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show mt-3">
                <?php echo $flashMessage['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
