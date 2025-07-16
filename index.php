<?php
session_start();
require "db.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 4;

$stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC");
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($newsList);

$start = ($page - 1) * $perPage;

$paginatedNews = array_slice($newsList, $start, $perPage);

$firstNewsOnPage = !empty($paginatedNews) ? $paginatedNews[0] : null;

// Функция для форматирования контента
function format_content($html) {
    // Разрешение тегов <p>, <strong>, <em> и <i>
    $allowedTags = '<p><strong><b><em><i>';
    return str_replace('</p>', '<br><br>', strip_tags($html, $allowedTags));
}

$totalPages = ceil($total / $perPage);
$currentPage = $page;

// Количество страниц в блоке
$visiblePages = 3;

// Определение начало текущего блока
$startPage = max(1, min($currentPage - 1, $totalPages - $visiblePages + 1));
$endPage = min($startPage + $visiblePages - 1, $totalPages);

// Навигация
$prevPage = $currentPage > 1 ? $currentPage - 1 : null;
$nextPage = $currentPage < $totalPages ? $currentPage + 1 : null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галактический вестник</title>
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
        display: flex;
        min-height: 100vh;
        margin: 0;
    }
    nav {
        width: 250px;
        background-color: #f0f0f0;
        padding: 20px;
        position: fixed;
        height: 100%;
        overflow-y: auto;
    }
    nav a {
        display: block;
        padding: 10px;
        margin-bottom: 5px;
        background-color: lightblue;
        text-decoration: none;
        color: black;
        border-radius: 4px;
    }
    nav a:hover {
        background-color: darkblue;
        color: white;
    }
    .main-content {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
    }
    @media (max-width: 768px) {
        body {
                flex-direction: column;
        }
        nav {
            width: 100%;
            position: relative;
            height: auto;
        }
        .main-content {
            margin-left: 0;
        }
    }

    .background-news-block {
        background-size: cover;
        background-position: center;
        color: white;
        padding: 60px 20px;
        border-radius: 10px;
        margin-top: 40px;
        position: relative;
        min-height: 250px;
        display: flex;
        align-items: center;
    }
    .background-news-overlay {
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1;
        border-radius: 10px;
    }
    .background-news-content {
        position: relative;
        z-index: 2;
    }

    .news-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .news-card {
        width: calc(50% - 10px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-card img {
        width: 100%;
        height: auto;
        display: block;
    }

    .news-content {
        padding: 16px;
        flex-grow: 1;
    }

    .news-content h3 {
        margin-top: 0;
        font-size: 1.2em;
    }
    .news-card h3 {
        transition: color 0.3s ease;
    }

    .news-card:hover h3 {
        color: #c2185b;
    }
    .news-content p {
        margin: 8px 0;
        color: #555;
    }

    .custom-pagination {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        font-family: sans-serif;
    }

    .custom-pagination .active {
        background-color: #007BFF;
        color: white;
        border-color: #007BFF;
    }
    .custom-pagination {
        display: flex;
        flex-direction: row;
        gap: 10px;
        align-items: flex-end;
    }

    .custom-pagination a {
        text-decoration: none;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
        transition: background-color 0.2s;
        min-width: 30px;
        text-align: center;
    }
    .custom-pagination a:hover,
    .custom-pagination .active {
        background-color: #007BFF;
        color: white;
        border-color: #007BFF;
    }
    .page-footer {
        margin-top: 60px;
        color: #555;
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
    </style>
</head>
<body>
<div class="main-content">
<div class="header-container">
    <img src="images/logo.png" alt="Логотип" class="header-logo">
    <h1>ГАЛАКТИЧЕСКИЙ ВЕСТНИК</h1>
</div>

    <?php if ($firstNewsOnPage): ?>
        <div class="background-news-block" style="background-image: url('images/<?= htmlspecialchars($firstNewsOnPage['image']) ?>');">
            <div class="background-news-overlay"></div>
            <div class="background-news-content">
                <h2><b><?= htmlspecialchars($firstNewsOnPage['title']) ?></b></h2>
                <p><?= nl2br(format_content($firstNewsOnPage['announce'])) ?></p>
            </div>
        </div>
    <?php else: ?>
        <p>Нет новостей на этой странице.</p>
    <?php endif; ?>

<section id="news">
    <h2 class="display-4">Новости</h2>
 
    <div class="news-container">
        <div class="news-grid">
            <?php foreach ($paginatedNews as $item): ?>
                <div class="news-card">
                    <div class="news-content">
                        <?php
$dateString = $item['date'];
$dateObject = date_create($dateString);
$formattedDate = $dateObject ? date_format($dateObject, 'd.m.Y') : '—';
?>
                        <p><?= $formattedDate ?> </p>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><?= format_content($item['announce']) ?></p>
                        <a class = "pink-button" href="news.php?id=<?= $item['id'] ?>">Подробнее &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php if ($totalPages > 1): ?>
    <div class="custom-pagination">
        <?php if ($prevPage): ?>
            <a href="?page=<?= $prevPage ?>">&larr;</a>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=<?= $i ?>"<?= $i == $currentPage ? ' class="active"' : '' ?>>
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($nextPage): ?>
            <a href="?page=<?= $nextPage ?>">&rarr;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</section>
<footer class="page-footer">
    <?php
    $date = !empty($item['date']) ? date_create($item['date']) : false;
    $yearFromDB = $date ? date_format($date, 'Y') : date('Y');
    ?>
    <p><hr><br></p>
    <p>&copy; <?= date('Y') . "-" . $yearFromDB ?> "Галактический вестник"</p>
</footer>
</body>
</html>