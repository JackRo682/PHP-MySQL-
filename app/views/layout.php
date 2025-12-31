<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../csrf.php';
function h($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
$user = current_user();
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Receipt App</title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/app.js" defer></script>
</head>
<body>
<header class="topbar">
    <div class="logo">📦 인수증 관리</div>
    <nav>
        <?php if ($user): ?>
            <a href="/receipts">인수증 리스트</a>
            <a href="/receipts/create">인수증 생성</a>
            <?php if (is_admin()): ?><a href="/templates">템플릿 관리</a><?php endif; ?>
            <span class="user-info"><?=h($user['username'])?> (<?=h($user['role'])?>)</span>
            <a href="/logout">로그아웃</a>
        <?php else: ?>
            <a href="/login">로그인</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <?php if (!empty($flash)): ?>
        <div class="flash"><?=h($flash)?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="error"><?=h($error)?></div>
    <?php endif; ?>
    <?php echo $content ?? ''; ?>
</main>
</body>
</html>
