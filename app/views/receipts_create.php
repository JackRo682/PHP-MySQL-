<?php ob_start(); ?>
<h2>인수증 생성</h2>
<div class="template-buttons" id="template-buttons"></div>
<form method="post" action="/receipts/store" id="receipt-form">
    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
    <section class="grid">
        <div>
            <h3>고객정보</h3>
            <label>주문자명 <input type="text" name="orderer_name" required></label>
            <label>주문자휴대전화1 <input type="text" name="orderer_phone1"></label>
            <label>주문자휴대전화2 <input type="text" name="orderer_phone2"></label>
            <label>수취인명 <input type="text" name="receiver_name"></label>
            <label>수취인휴대전화1 <input type="text" name="receiver_phone1"></label>
            <label>수취인휴대전화2 <input type="text" name="receiver_phone2"></label>
        </div>
        <div>
            <h3>배송정보</h3>
            <label>배달일 <input type="date" name="delivery_date"></label>
            <label>배달시간
                <select name="delivery_time_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['delivery_time'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>배달장소 <input type="text" name="delivery_place"></label>
            <label>우편번호 <input type="text" name="postal_code"></label>
            <label>주소 <input type="text" name="address"></label>
            <label>상세주소 <input type="text" name="address_detail"></label>
            <label>배달요청사항 <textarea name="delivery_request"></textarea></label>
        </div>
        <div>
            <h3>상품/옵션</h3>
            <label>배송방법
                <select name="delivery_method_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['delivery_method'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>화분사이즈
                <select name="pot_size_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['pot_size'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>화분종류
                <select name="pot_type_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['pot_type'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>화분색상
                <select name="pot_color_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['pot_color'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>식물사이즈
                <select name="plant_size_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['plant_size'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>식물종류
                <select name="plant_type_id">
                    <option value="">--선택--</option>
                    <?php foreach ($codes['plant_type'] as $c): ?>
                        <option value="<?=h($c['id'])?>"><?=h($c['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>부자재
                <div class="checkboxes" id="accessory-boxes">
                    <?php foreach ($accessories as $a): ?>
                        <label><input type="checkbox" name="accessories[]" value="<?=h($a['id'])?>"> <?=h($a['name'])?></label>
                    <?php endforeach; ?>
                </div>
            </label>
            <label>판매금액 <input type="number" name="sale_amount" value="0"></label>
            <label>주문금액 <input type="number" name="order_amount" value="0"></label>
            <label>상태
                <select name="status">
                    <option value="PENDING">대기</option>
                    <option value="NEW">신규</option>
                </select>
            </label>
            <label>생성 개수 N (1~50) <input type="number" name="create_count" value="1" min="1" max="50"></label>
        </div>
    </section>
    <div class="actions"><button type="submit">저장</button></div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
