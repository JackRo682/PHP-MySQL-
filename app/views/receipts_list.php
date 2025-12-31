<?php ob_start(); ?>
<h2>인수증 리스트</h2>
<?php if (!empty($_GET['batch'])): ?>
    <p>배치 그룹: <?=h($_GET['batch'])?></p>
<?php endif; ?>
<form method="post" action="/receipts/batch-status" id="batch-form">
    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
    <div class="actions">
        <button type="submit" name="target_status" value="NEW_ARRIVED">신규도착완료</button>
        <button type="submit" name="target_status" value="SAME_DELIVERY_ARRIVED">동일배송도착완료</button>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="check-all"></th>
                <th>ID</th>
                <th>번호</th>
                <th>주문자</th>
                <th>수취인</th>
                <th>배달일</th>
                <th>상태</th>
                <th>작성자</th>
                <th>생성시각</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($receipts as $r): ?>
                <tr>
                    <td><input type="checkbox" name="receipt_ids[]" value="<?=h($r['id'])?>"></td>
                    <td><?=h($r['id'])?></td>
                    <td><?=h($r['receipt_no'])?></td>
                    <td><?=h($r['orderer_name'])?></td>
                    <td><?=h($r['receiver_name'])?></td>
                    <td><?=h($r['delivery_date'])?></td>
                    <td><?=h($r['status'])?></td>
                    <td><?=h($r['username'])?></td>
                    <td><?=h($r['created_at'])?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
