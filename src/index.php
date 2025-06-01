<?php
session_start(); // ← セッションを最初に開始する

// トークンがなければ新規発行
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

// config.php を読み込んで PDO インスタンスを使えるようにする
require_once __DIR__ . '/config.php';

// ページネーションの処理
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// 総投稿数を取得してページ数を計算
$countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

// 投稿をページ単位で取得（LIMIT + OFFSET）
$stmt = $pdo->prepare("SELECT id, user_id, name, title, content, created_at FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>


<?php
$flashMessage = '';
if (isset($_SESSION['flash'])) {
    $flashMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>

<div class="container">
    <h1>提示板</h1>

    <?php if ($flashMessage): ?>
        <div id="alert-message" class="alert alert-success">
            <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif ?>

    <!-- 投稿フォーム -->
    <div class="card">
        <form action="post.php" method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="name">名前（任意）</label>
                <input type="text" name="name" id="name" placeholder="例: 山田太郎">
            </div>

            <div class="form-group">
                <label for="title">タイトル</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="content">本文</label>
                <textarea name="content" id="content" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-post">投稿する</button>
        </form>
    </div>


    <h2>投稿一覧</h2>
    <p class="post-count">全 <?= $totalPosts ?> 件の投稿</p>

    <div class="card">
        <?php foreach ($posts as $index => $post): ?>
            <div class="card-wrap">

                <p class="card-title">
                    <?= ($offset + $index + 1) ?>. <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                    <span style="font-size: 14px; color: #888; margin-left: 10px;">
                        （<?= htmlspecialchars($post['name'] ?? '名無し', ENT_QUOTES, 'UTF-8') ?> /
                        <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?>）
                    </span>
                </p>

                <p class="card-text">
                    <?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) ?>
                </p>

                <!-- ログインユーザー自身の投稿の場合、編集・削除ボタンが出現 -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === (int)$post['user_id']): ?>

                    <!-- 削除ボタン -->
                    <form action="delete.php" method="post" style="display:inline; margin-right: 20px;">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('この投稿を削除しますか？');">削除</button>
                    </form>

                    <!-- 編集ボタン -->
                    <form action="edit.php" method="get" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" class="btn btn-edit">編集</button>
                    </form>

                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; 前へ</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" <?= $i === $page ? 'style="font-weight: bold;"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">次へ &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>