<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ReceiptController.php';
require_once __DIR__ . '/../app/controllers/TemplateController.php';

start_session();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/') {
    header('Location: /receipts');
    exit;
}

switch ($path) {
    case '/login':
        if ($method === 'GET') { show_login(); }
        elseif ($method === 'POST') { handle_login(); }
        break;
    case '/logout':
        handle_logout();
        break;
    case '/receipts/create':
        show_receipt_create();
        break;
    case '/receipts/store':
        if ($method === 'POST') { store_receipts(); }
        break;
    case '/receipts':
        list_receipts();
        break;
    case '/receipts/batch-status':
        if ($method === 'POST') { batch_status_update(); }
        break;
    case '/templates':
        list_templates();
        break;
    case '/templates/create':
        if ($method === 'GET') { show_template_form(); }
        elseif ($method === 'POST') { save_template(); }
        break;
    case '/templates/edit':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($method === 'GET') { show_template_form($id); }
        elseif ($method === 'POST') { save_template($id); }
        break;
    case '/templates/delete':
        if ($method === 'POST' && isset($_GET['id'])) { delete_template((int)$_GET['id']); }
        break;
    case '/api/templates':
        api_templates();
        break;
    case '/api/template':
        api_template((int)($_GET['id'] ?? 0));
        break;
    default:
        http_response_code(404);
        echo 'Not Found';
}
