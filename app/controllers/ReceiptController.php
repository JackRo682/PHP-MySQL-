<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../csrf.php';
require_once __DIR__ . '/../db.php';

function fetch_codes(string $group): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT id, name FROM codes WHERE group_code = ? ORDER BY sort_order');
    $stmt->execute([$group]);
    return $stmt->fetchAll();
}

function fetch_accessories(): array
{
    $pdo = get_pdo();
    return $pdo->query('SELECT id, name FROM accessory_items ORDER BY id')->fetchAll();
}

function generate_receipt_numbers(PDO $pdo, int $count): array
{
    $date = date('Ymd');
    $stmt = $pdo->prepare('SELECT last_seq FROM receipt_sequences WHERE seq_date = ? FOR UPDATE');
    $stmt->execute([$date]);
    $row = $stmt->fetch();
    if (!$row) {
        $pdo->prepare('INSERT INTO receipt_sequences (seq_date, last_seq) VALUES (?, 0)')->execute([$date]);
        $last = 0;
    } else {
        $last = (int)$row['last_seq'];
    }
    $newLast = $last + $count;
    $pdo->prepare('UPDATE receipt_sequences SET last_seq = ? WHERE seq_date = ?')->execute([$newLast, $date]);

    $numbers = [];
    for ($i = 1; $i <= $count; $i++) {
        $seq = $last + $i;
        $numbers[] = 'R-' . $date . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
    return $numbers;
}

function show_receipt_create(): void
{
    require_login();
    $groups = ['delivery_method', 'pot_size', 'pot_type', 'pot_color', 'plant_size', 'plant_type', 'delivery_time'];
    $codes = [];
    foreach ($groups as $g) {
        $codes[$g] = fetch_codes($g);
    }
    $accessories = fetch_accessories();
    include __DIR__ . '/../views/receipts_create.php';
}

function store_receipts(): void
{
    require_login();
    verify_csrf();
    $pdo = get_pdo();
    $user = current_user();

    $count = max(1, min(50, (int)($_POST['create_count'] ?? 1)));

    $data = [
        'orderer_name' => trim($_POST['orderer_name'] ?? ''),
        'orderer_phone1' => trim($_POST['orderer_phone1'] ?? ''),
        'orderer_phone2' => trim($_POST['orderer_phone2'] ?? ''),
        'receiver_name' => trim($_POST['receiver_name'] ?? ''),
        'receiver_phone1' => trim($_POST['receiver_phone1'] ?? ''),
        'receiver_phone2' => trim($_POST['receiver_phone2'] ?? ''),
        'delivery_date' => $_POST['delivery_date'] ?? null,
        'delivery_time_id' => $_POST['delivery_time_id'] ?? null,
        'delivery_place' => trim($_POST['delivery_place'] ?? ''),
        'postal_code' => trim($_POST['postal_code'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'address_detail' => trim($_POST['address_detail'] ?? ''),
        'delivery_request' => trim($_POST['delivery_request'] ?? ''),
        'delivery_method_id' => $_POST['delivery_method_id'] ?? null,
        'pot_size_id' => $_POST['pot_size_id'] ?? null,
        'pot_type_id' => $_POST['pot_type_id'] ?? null,
        'pot_color_id' => $_POST['pot_color_id'] ?? null,
        'plant_size_id' => $_POST['plant_size_id'] ?? null,
        'plant_type_id' => $_POST['plant_type_id'] ?? null,
        'sale_amount' => (int)($_POST['sale_amount'] ?? 0),
        'order_amount' => (int)($_POST['order_amount'] ?? 0),
        'status' => $_POST['status'] ?? 'PENDING',
    ];

    $accessories = array_map('intval', $_POST['accessories'] ?? []);

    try {
        $pdo->beginTransaction();
        $numbers = generate_receipt_numbers($pdo, $count);
        $batchGroup = $count > 1 ? uniqid('batch') : null;
        $insert = $pdo->prepare('INSERT INTO receipts (receipt_no, orderer_name, orderer_phone1, orderer_phone2, receiver_name, receiver_phone1, receiver_phone2, delivery_date, delivery_time_id, delivery_place, postal_code, address, address_detail, delivery_request, delivery_method_id, pot_size_id, pot_type_id, pot_color_id, plant_size_id, plant_type_id, sale_amount, order_amount, status, created_by, created_at, updated_at, batch_group_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW(),?)');
        $accStmt = $pdo->prepare('INSERT INTO receipt_accessories (receipt_id, accessory_item_id) VALUES (?, ?)');

        foreach ($numbers as $receiptNo) {
            $insert->execute([
                $receiptNo,
                $data['orderer_name'],
                $data['orderer_phone1'],
                $data['orderer_phone2'],
                $data['receiver_name'],
                $data['receiver_phone1'],
                $data['receiver_phone2'],
                $data['delivery_date'],
                $data['delivery_time_id'],
                $data['delivery_place'],
                $data['postal_code'],
                $data['address'],
                $data['address_detail'],
                $data['delivery_request'],
                $data['delivery_method_id'],
                $data['pot_size_id'],
                $data['pot_type_id'],
                $data['pot_color_id'],
                $data['plant_size_id'],
                $data['plant_type_id'],
                $data['sale_amount'],
                $data['order_amount'],
                $data['status'],
                $user['id'],
                $batchGroup,
            ]);
            $receiptId = (int)$pdo->lastInsertId();
            foreach ($accessories as $aid) {
                $accStmt->execute([$receiptId, $aid]);
            }
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo 'Error: ' . htmlspecialchars($e->getMessage());
        exit;
    }

    if ($count > 1) {
        header('Location: /receipts?batch=' . urlencode($batchGroup));
    } else {
        header('Location: /receipts');
    }
}

function list_receipts(): void
{
    require_login();
    $pdo = get_pdo();
    $params = [];
    $where = '';
    if (!empty($_GET['batch'])) {
        $where = 'WHERE batch_group_id = ?';
        $params[] = $_GET['batch'];
    }
    $stmt = $pdo->prepare("SELECT r.*, u.username FROM receipts r LEFT JOIN users u ON r.created_by = u.id $where ORDER BY r.id DESC LIMIT 200");
    $stmt->execute($params);
    $receipts = $stmt->fetchAll();
    $flash = get_flash('success');
    include __DIR__ . '/../views/receipts_list.php';
}

function batch_status_update(): void
{
    require_login();
    verify_csrf();
    $ids = array_map('intval', $_POST['receipt_ids'] ?? []);
    $target = $_POST['target_status'] ?? '';
    $allowed = ['NEW_ARRIVED', 'SAME_DELIVERY_ARRIVED'];
    if (empty($ids) || count($ids) > 500 || !in_array($target, $allowed, true)) {
        set_flash('success', '잘못된 요청입니다.');
        header('Location: /receipts');
        exit;
    }
    $pdo = get_pdo();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $user = current_user();
    $batchId = uniqid('status');
    try {
        $pdo->beginTransaction();
        $select = $pdo->prepare("SELECT id, status FROM receipts WHERE id IN ($placeholders) FOR UPDATE");
        $select->execute($ids);
        $rows = $select->fetchAll();
        $update = $pdo->prepare('UPDATE receipts SET status = ?, updated_at = NOW() WHERE id = ?');
        $log = $pdo->prepare('INSERT INTO receipt_status_logs (receipt_id, from_status, to_status, changed_by, changed_at, change_batch_id) VALUES (?,?,?,?,NOW(),?)');
        foreach ($rows as $row) {
            $update->execute([$target, $row['id']]);
            $log->execute([$row['id'], $row['status'], $target, $user['id'], $batchId]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo 'Error: ' . htmlspecialchars($e->getMessage());
        exit;
    }
    set_flash('success', count($rows) . '건 상태가 변경되었습니다.');
    header('Location: /receipts');
}
