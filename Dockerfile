FROM php:8.2-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Composerインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ設定
WORKDIR /var/www

# プロジェクトファイルをコピー
COPY . .

# ポート指定（Renderは自動で公開）
EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
