<?php
session_start();
require_once __DIR__ . '/config.php';

// CSRFトークン
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    $_SESSION['flash'] = '不正なリクエストです（CSRFトークン）。';
    header('Location: index.php');
    exit;
}

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = 'ログインしてください。';
    header('Location: login.php');
    exit;
}

// 入力データ取得
$postId = (int)($_POST['post_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// バリデーションチェック
if ($title === '' || $content === '') {
    $_SESSION['flash'] = 'タイトルと本文は必須です。';
    header("Location: edit.php?id=$postId");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $postId, $_SESSION['user_id']]);

    $_SESSION['flash'] = '投稿を更新しました。';
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = '更新中にエラーが発生しました。';
    header("Location: edit.php?id=$postId");
    exit;
}