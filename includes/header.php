<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="manifest" href="<?php echo APP_URL; ?>/manifest.json">
    <meta name="theme-color" content="#4a90e2">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo APP_URL; ?>/service-worker.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }
    </script>
</head>
<body class="dashboard-body">
    <nav class="main-nav">
        <div class="nav-container">
            <a href="<?php echo APP_URL; ?>/index.php" class="brand"><?php echo APP_NAME; ?></a>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo APP_URL; ?>/index.php">Dashboard</a></li>
                    <?php if ($_SESSION['user_role'] === 'system_admin' || $_SESSION['user_role'] === 'school_admin'): ?>
                        <li><a href="<?php echo APP_URL; ?>/modules/admin/admin_dashboard.php" style="color: #ffcc00; font-weight: bold;">Admin Control Center</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo APP_URL; ?>/logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo APP_URL; ?>/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">
