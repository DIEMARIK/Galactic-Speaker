<?php
require 'db.php';

if (!isset($_GET['id'])) {
    echo "Новость не найдена.";
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    echo "Новость не найдена.";
    exit;
}

function format_content($html) {
    $allowedTags = '<p><strong><b><em><i>';
    return str_replace('</p>', '<br><br>', strip_tags($html, $allowedTags));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($news['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    .header-container {
        display: flex;
        align-items: center;
        gap: 20px; 
        margin-bottom: 20px;
        color: #8a2be2;
    }
    .header-logo {
        max-width: 10%;
        height: auto;
        border-radius: 8px;
    }
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        max-width: 1000px;
        margin: auto;
        padding: 20px;
    }

    h1.display-4 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    hr {
        border: none;
        border-top: 1px solid #ccc;
        margin: 20px 0;
    }
    .breadcrumb {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 20px;
    }

    .breadcrumb a,
    .breadcrumb span {
        text-decoration: none;
        color: #333;
    }

    .breadcrumb .divider {
        margin: 0 8px;
        color: #999;
    }

    .breadcrumb-link {
        background-color: #e0f7fa;
        padding: 5px 10px;
        border-radius: 4px;
        color: #007BFF;
    }

    .breadcrumb-link:hover {
        background-color: #b2ebf2;
    }

    .active-breadcrumb {
        font-weight: bold;
        color: #333;
    }
    .news-detail {
        display: flex;
        gap: 40px;
        align-items: flex-start;
        flex-wrap: wrap; /* Для мобильных устройств */
    }

    .news-text {
        flex: 1;
    }

    .news-image {
        flex: 1;
        min-width: 250px;
    }

    .news-image img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .news-date {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 10px;
    }

    .pink-bold {
        font-weight: bold;
        color: #c2185b;
        margin-top: 0;
        font-size: 1.5rem;
        line-height: 1.4;
    }

    .pink-button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 16px;
        color: #c2185b;
        background-color: transparent;
        border: 2px solid #c2185b;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .pink-button:hover {
        color: #fff;
        background-color: #c2185b;
        border-color: #c2185b;
    }

    .page-footer {
        margin-top: 60px;
        color: #555;
        text-align: center;
    }
    </style>
</head>
<body>

<div class="header-container">
    <img src="images/logo.png" alt="Логотип" class="header-logo">
    <h1>ГАЛАКТИЧЕСКИЙ ВЕСТНИК</h1>
</div>
<hr>

<nav class="breadcrumb">
    <a href="index.php" class="breadcrumb-link">Главная</a>
    <span class="divider">/</span>
    <span class="active-breadcrumb"><?= htmlspecialchars($news['title']) ?></span>
</nav>

<h1><?= htmlspecialchars($news['title']) ?></h1>

<div class="news-detail">
    <div class="news-text">
        <?php
        $dateString = $news['date'];
        $dateObject = date_create($dateString);
        $formattedDate = $dateObject ? date_format($dateObject, 'd.m.Y') : '—';
        ?>
        <p class="news-date"><?= $formattedDate ?></p>
        <p class="pink-bold">
            <?= nl2br(htmlspecialchars(strip_tags($news['announce']))) ?>
        </p>
        <?php
        $paragraphs = array_filter(array_map('trim', explode('<br><br>', format_content($news['content']))));
        foreach ($paragraphs as $paragraph): ?>
            <p><?= $paragraph ?></p>
        <?php endforeach; ?>
        <?php
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        $backLink = $referer;
        ?>
        <a href="<?= htmlspecialchars($backLink) ?>" class="pink-button">&larr; Назад к новостям</a>
    </div>

    <div class="news-image">
        <img src="images/<?= htmlspecialchars($news['image']) ?>" alt="Фото новости">
    </div>
</div>

<footer class="page-footer">
    <hr>
    <p>&copy; <?= date('Y') ?>–<?= date_format(date_create($news['date']), 'Y') ?> "Галактический вестник"</p>
</footer>

</body>
</html>