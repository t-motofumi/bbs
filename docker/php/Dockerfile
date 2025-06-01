# ベースイメージに Apache + PHP を使用
FROM php:8.2-apache

# OSパッケージ更新と必要ライブラリのインストール
RUN apt-get update && \
    apt-get install -y libonig-dev libzip-dev zip unzip && \
    docker-php-ext-install pdo pdo_mysql mysqli mbstring

# タイムゾーンを日本時間に設定（任意）
ENV TZ=Asia/Tokyo

# Apacheのドキュメントルートを /var/www/html に設定（デフォルトなので省略可）
# COPY ./src /var/www/html ← docker-compose のvolumes でマウントするなら不要