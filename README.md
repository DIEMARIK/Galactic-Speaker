# Галактический вестник
Сайт о галактических новостях, сделанный на PHP с использованием CSS и MySQL.

## Структура проекта
- `index.php` — главная страница
- `news.php` — динамическая страница для каждой отдельной новости
- `db.php` — подключение к базе данных
- `images/` — изображения

## Установка
Скачайте репозиторий с файлами проекта:

Через кнопку Code → Download ZIP на GitHub
или с помощью Git:

https://github.com/DIEMARIK/Galactic-Speaker.git

Распакуйте (если скачали ZIP) и разместите папку проекта в директории веб-сервера:

Для OpenServer / XAMPP / MAMP — в папку htdocs или domains

Для локального Apache — в корень, например /var/www/html

Настройте подключение к базе данных:

Файл db.php должен содержать корректные данные подключения к MySQL:

$pdo = new PDO("mysql:host=localhost;dbname=techart_web;charset=utf8", "root", ""); Импортируйте структуру базы данных:

Откройте phpMyAdmin или MySQL-клиент

Откройте сайт в браузере:

http://localhost/techartweb
