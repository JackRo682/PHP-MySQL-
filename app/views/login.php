<?php ob_start(); ?>
<h2>로그인</h2>
<form method="post" action="/login">
    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
    <label>아이디 <input type="text" name="username" required></label>
    <label>비밀번호 <input type="password" name="password" required></label>
    <button type="submit">로그인</button>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
