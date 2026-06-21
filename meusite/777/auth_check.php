<?php
/**
 * auth_check.php — Segurança Centralizada Cyborg Google Ads
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax'
    ]);
}

date_default_timezone_set('America/Sao_Paulo');

$correct_hash = 'fc1b7a9976fa0826a827f334c30925963eb27dbc37048fddaeedf20a5edc1ab4';
$error = '';

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $pass = $_POST['password'] ?? '';
    if (hash('sha256', $pass) === $correct_hash) {
        $_SESSION['cyborg_googleads_auth'] = true;
        header('Location: ' . $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
        exit;
    } else {
        $error = 'CHAVE DE ORQUESTRADOR INCORRETA. ACESSO NEGADO.';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['cyborg_googleads_auth']);
    header('Location: googleads_dashboard.php');
    exit;
}

$authenticated = !empty($_SESSION['cyborg_googleads_auth']);
