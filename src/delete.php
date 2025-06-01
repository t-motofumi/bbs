<?php
session_start();
require_once __DIR__ . '/config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = 'ログインが必要です。';
    header('Location: login.php');
    exit;
}

// CSRFチェック
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    $_SESSION['flash'] = '不正なリクエストです（CSRFトークン）。';
    header('Location: index.php');
    exit;
}

// 投稿IDを取得
$postId = (int)($_POST['post_id'] ?? 0);

try {
    // 投稿がログインユーザーのものであることを確認して削除
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
    $stmt->execute([$postId, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['flash'] = '投稿を削除しました。';
    } else {
        $_SESSION['flash'] = '削除できません（自分の投稿ではないか、存在しません）。';
    }
    
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = '削除処理中にエラーが発生しました。';
    header('Location: index.php');
    exit;
}