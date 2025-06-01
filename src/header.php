<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="シンプルな提示板です。">
    <meta name="robots" content="index, follow">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : '提示板' ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="wrapper">

<!-- ナビゲーションバー -->
<header class="site-header">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">提示板</a>
        <nav class="nav-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-text">
                    こんにちは、<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?> さん
                </span>
                <a href="logout.php" class="nav-link">ログアウト</a>
            <?php else: ?>
                <a href="register.php" class="nav-link">会員登録</a>
                <a href="login.php" class="nav-link">ログイン</a>
            <?php endif; ?>
        </nav>
    </div>
</header>