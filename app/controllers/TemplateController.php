<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../csrf.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/ReceiptController.php';

function list_templates(): void
{
    require_admin();
    $pdo = get_pdo();
    $templates = $pdo->query('SELECT * FROM templates ORDER BY id DESC')->fetchAll();
    $flash = get_flash('success');
    include __DIR__ . '/../views/templates_index.php';
}

function show_template_form(?int $id = null): void
{
    require_admin();
    $pdo = get_pdo();
    $template = null;
    $groups = ['delivery_method', 'pot_size', 'pot_type', 'pot_color', 'plant_size', 'plant_type', 'delivery_time'];
    $codes = [];
    foreach ($groups as $g) {
        $codes[$g] = fetch_codes($g);
    }
    $accessories = fetch_accessories();
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM templates WHERE id = ?');
        $stmt->execute([$id]);
        $template = $stmt->fetch();
    }
    include __DIR__ . '/../views/templates_form.php';
}

function save_template(?int $id = null): void
{
    require_admin();
    verify_csrf();
    $pdo = get_pdo();
    $name = trim($_POST['name'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $data = $_POST['template_data'] ?? [];
    $user = current_user();

    $activeCount = (int)$pdo->query('SELECT COUNT(*) FROM templates WHERE is_active = 1')->fetchColumn();
    if ($is_active && !$id && $activeCount >= 10) {
        set_flash('success', '활성 템플릿은 최대 10개까지 가능합니다.');
        header('Location: /templates');
        exit;
    }

    if ($is_active && $id) {
        $stmt = $pdo->prepare('SELECT is_active FROM templates WHERE id = ?');
        $stmt->execute([$id]);
        $currentActive = (int)$stmt->fetchColumn();
        if (!$currentActive && $activeCount >= 10) {
            set_flash('success', '활성 템플릿은 최대 10개까지 가능합니다.');
            header('Location: /templates');
            exit;
        }
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    if ($id) {
        $stmt = $pdo->prepare('UPDATE templates SET name = ?, is_active = ?, data_json = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$name, $is_active, $json, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO templates (name, is_active, data_json, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([$name, $is_active, $json, $user['id']]);
    }
    set_flash('success', '저장되었습니다.');
    header('Location: /templates');
}

function delete_template(int $id): void
{
    require_admin();
    verify_csrf();
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM templates WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', '삭제되었습니다.');
    header('Location: /templates');
}

function api_templates(): void
{
    require_login();
    header('Content-Type: application/json');
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT id, name FROM templates WHERE is_active = 1 ORDER BY id ASC LIMIT 10');
    echo json_encode($stmt->fetchAll());
}

function api_template(int $id): void
{
    require_login();
    header('Content-Type: application/json');
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT data_json FROM templates WHERE id = ? AND is_active = 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        echo $row['data_json'];
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Template not found']);
    }
}
