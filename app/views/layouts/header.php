<?php
$userType = $_SESSION['user_type'] ?? null;
$isAdmin = isset($_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'JeevanDaan'; ?> | JeevanDaan - Blood Donation Nepal</title>
    <link rel="icon" type="image/png" href="<?php echo APP_URL; ?>/images/logo.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="<?php echo APP_URL; ?>" class="logo">
                <img src="<?php echo APP_URL; ?>/images/logo.png" alt="JeevanDaan" style="height:45px;width:auto;">
                <span>JeevanDaan</span>
            </a>
            
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="<?php echo APP_URL; ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo APP_URL; ?>/requests"><i class="fas fa-tint"></i> Blood Requests</a></li>
                    <li><a href="<?php echo APP_URL; ?>/home/learn"><i class="fas fa-book"></i> Learn</a></li>
                    <li><a href="<?php echo APP_URL; ?>/home/about"><i class="fas fa-info-circle"></i> About</a></li>
                </ul>
                
                <div class="nav-auth">
                    <?php if ($userType === 'user'): ?>
                        <a href="<?php echo APP_URL; ?>/user/dashboard" class="btn btn-outline">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    <?php elseif ($userType === 'organization'): ?>
                        <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                            <i class="fas fa-building"></i> Dashboard
                        </a>
                    <?php elseif ($isAdmin): ?>
                        <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php else: ?>
                        <a href="<?php echo APP_URL; ?>/auth/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="main">
