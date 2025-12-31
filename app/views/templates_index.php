<?php ob_start(); ?>
<h2>템플릿 관리</h2>
<div class="actions">
    <a class="button" href="/templates/create">+ 새 템플릿</a>
</div>
<table>
    <thead>
        <tr><th>ID</th><th>이름</th><th>활성</th><th>수정</th><th>삭제</th></tr>
    </thead>
    <tbody>
        <?php foreach ($templates as $t): ?>
            <tr>
                <td><?=h($t['id'])?></td>
                <td><?=h($t['name'])?></td>
                <td><?= $t['is_active'] ? 'Y' : 'N' ?></td>
                <td><a href="/templates/edit?id=<?=h($t['id'])?>">수정</a></td>
                <td>
                    <form method="post" action="/templates/delete?id=<?=h($t['id'])?>" onsubmit="return confirm('삭제?');">
                        <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
                        <button type="submit">삭제</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
