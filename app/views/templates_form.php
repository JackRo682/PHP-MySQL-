<?php ob_start(); ?>
<?php $values = $template ? json_decode($template['data_json'], true) : []; ?>
<h2>템플릿 <?= $template ? '수정' : '생성' ?></h2>
<form method="post" action="<?= $template ? '/templates/edit?id=' . h($template['id']) : '/templates/create' ?>">
    <input type="hidden" name="csrf_token" value="<?=csrf_token()?>">
    <label>템플릿명 <input type="text" name="name" value="<?=h($template['name'] ?? '')?>" required></label>
    <label><input type="checkbox" name="is_active" value="1" <?=(!empty($template['is_active']))?'checked':''?>> 활성</label>
    <fieldset>
        <legend>저장할 값</legend>
        <label>주문자명 <input type="text" name="template_data[orderer_name]" value="<?=h($values['orderer_name'] ?? '')?>"></label>
        <label>수취인명 <input type="text" name="template_data[receiver_name]" value="<?=h($values['receiver_name'] ?? '')?>"></label>
        <label>배달요청사항 <textarea name="template_data[delivery_request]"><?php echo h($values['delivery_request'] ?? ''); ?></textarea></label>
        <label>배송방법
            <select name="template_data[delivery_method_id]">
                <option value="">--</option>
                <?php foreach ($codes['delivery_method'] as $c): ?>
                    <option value="<?=h($c['id'])?>" <?=($values['delivery_method_id'] ?? '') == $c['id'] ? 'selected' : ''?>><?=h($c['name'])?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>배달시간
            <select name="template_data[delivery_time_id]">
                <option value="">--</option>
                <?php foreach ($codes['delivery_time'] as $c): ?>
                    <option value="<?=h($c['id'])?>" <?=($values['delivery_time_id'] ?? '') == $c['id'] ? 'selected' : ''?>><?=h($c['name'])?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>화분종류
            <select name="template_data[pot_type_id]">
                <option value="">--</option>
                <?php foreach ($codes['pot_type'] as $c): ?>
                    <option value="<?=h($c['id'])?>" <?=($values['pot_type_id'] ?? '') == $c['id'] ? 'selected' : ''?>><?=h($c['name'])?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>부자재</label>
        <div class="checkboxes">
            <?php foreach ($accessories as $a): ?>
                <label><input type="checkbox" name="template_data[accessories][]" value="<?=h($a['id'])?>" <?=(isset($values['accessories']) && in_array($a['id'], $values['accessories'])) ? 'checked' : ''?>> <?=h($a['name'])?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>
    <div class="actions"><button type="submit">저장</button></div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
