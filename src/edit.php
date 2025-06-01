<?php
session_start();
require_once __DIR__ . '/config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = 'ログインしてください。';
    header('Location: login.php');
    exit;
}

// 投稿IDの取得
$postId = (int)($_GET['id'] ?? 0);

// 投稿を取得して、自分の投稿か確認
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? AND user_id = ?');
$stmt->execute([$postId, $_SESSION['user_id']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['flash'] = '投稿が見つからないか、編集権限がありません。';
    header('Location: index.php');
    exit;
}

// CSRF トークン
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

include 'header.php';
?>

<div class="container">
    <h2>投稿を編集</h2>
    <form action="update.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
            <label for="content">本文</label>
            <textarea name="content" id="content" rows="4" required><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">更新する</button>
    </form>
</div>

<?php include 'footer.php'; ?>
