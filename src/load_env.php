<?php
function loadEnv($path = __DIR__ . '/.env') {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0) continue; # コメント行はスキップ
        if (strpos($line, '=') === false) continue; # 無効な行もスキップ

        list($key, $value) = explode('=', $line, 2); # キーとバリューを分ける
        $key = trim($key); # trim()で前後の空白を除去して、キーを保存
        $value = trim($value); # trim()で前後の空白を除去して、バリューを保存

        // シングルクオートやダブルクオートで囲まれていれば除去
        if (
            strlen($value) >= 2 && # 値が2以上でクオートされている場合のみ除去
            (
                (strpos($value, '"') === 0 && substr($value, -1) === '"') || 
                (strpos($value, "'") === 0 && substr($value, -1) === "'")
            )
        ) {
            $value = substr($value, 1, -1);
        }

        putenv("$key=$value"); # PHPの環境変数として登録する
    }
}