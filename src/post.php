<?php
session_start();

// CSRFトークン確認
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    $_SESSION['flash'] = '不正な投稿です（CSRFトークンエラー）。';
    header('Location: index.php');
    exit();
}

// 入力値取得
$name = $_POST['name'] ?? '';
if ($name === '') {
    $name = '名無し';
}
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// バリデーション（未入力・長さ制限）
if ($title === '' || $content === ''){
    $_SESSION['flash'] = 'すべての項目を入力してください。';
    header('Location: index.php');
    exit();
}

if (mb_strlen($name) > 100 || mb_strlen($title) > 100 || mb_strlen($content) > 1000) {
    $_SESSION['flash'] = '入力が長すぎます。';
    header('Location: index.php');
    exit();
}

// データベースに接続
$dsn = 'mysql:host=mysql;dbname=bbs;charset=utf8mb4';
$user = 'user';
$password = 'password';

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    $_SESSION['flash'] = 'データベース接続エラー。';
    header('Location: index.php');
    exit();
}

// 投稿を保存（プリペアドステートメント）
try {
    // ログインしているユーザーのIDを取得
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash'] = 'ログインが必要です。';
        header('Location: login.php');
        exit();
    }
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, name, title, content, created_at) VALUES (:user_id, :name, :title, :content, NOW())");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['flash'] = '投稿が完了しました。';
    header('Location: index.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['flash'] = '投稿に失敗しました。' . $e->getMessage();
    header('Location: index.php');
    exit();
}

?>