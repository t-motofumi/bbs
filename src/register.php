<?php
session_start();
require_once __DIR__ . '/config.php';

// CSRFトークンの生成（初回のみ）
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRFチェック
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        $_SESSION['register_error'] = '不正なリクエストです（CSRFトークン）。';
        header('Location: register.php');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['register_error'] = 'ユーザー名とパスワードは必須です。';
        header('Location: register.php');
        exit;
    }

    try {
        // ユーザー名の重複チェック
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['register_error'] = 'そのユーザー名は既に使われています。';
            header('Location: register.php');
            exit;
        }

        // パスワードハッシュ化
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // INSERT
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, $passwordHash]);

        // 登録成功時リダイレクト
        $_SESSION['flash'] = 'ユーザー登録が完了しました。ログインしてください。';
        header('Location: login.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['register_error'] = 'データベースエラーが発生しました。';
        header('Location: register.php');
        exit;
    }
}
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>ユーザー登録</h2>

    <?php if (isset($_SESSION['register_error'])): ?>
        <div>
            <?= htmlspecialchars($_SESSION['register_error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>
    
    <div class="card">
        <form action="register.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" id="username" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary">登録</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>