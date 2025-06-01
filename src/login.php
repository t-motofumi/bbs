<?php
session_start();
require_once __DIR__ . '/config.php';

// POST処理（ログイン処理）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['login_error'] = 'ユーザー名とパスワードを入力してください。';
        header('Location: login.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['flash'] = 'ログインに成功しました。';
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['login_error'] = 'ユーザー名またはパスワードが違います。';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = 'ログイン処理中にエラーが発生しました。';
        header('Location: login.php');
        exit;
    }
}
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>ログイン</h2>

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <div class="card">
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">ユーザー名</label>
                <input type="text" name="username" id="username" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary">ログイン</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>