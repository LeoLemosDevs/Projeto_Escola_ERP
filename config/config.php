<?php
/**
 * Master School ERP - Configurações Gerais do Sistema
 * Define constantes globais, controle de sessão e funções utilitárias.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Constantes Básicas da Aplicação
define('APP_NAME', 'Master School');
define('APP_SUBTITLE', 'Excellence in Brazilian & International Education');
define('APP_VERSION', '1.0.0');

// Identificação automática da BASE_URL na raiz do projeto
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$scriptDir = str_replace('\\', '/', $scriptDir);

// Remove subdiretórios do ERP caso a página esteja rodando dentro de um portal
$subdirs = ['/admin', '/aluno', '/professor', '/config', '/includes', '/api', '/database', '/assets', '/doc'];
foreach ($subdirs as $sub) {
    if (substr($scriptDir, -strlen($sub)) === $sub) {
        $scriptDir = substr($scriptDir, 0, -strlen($sub));
        break;
    }
}

$baseDir = rtrim($scriptDir, '/');
define('BASE_URL', $protocol . $host . $baseDir);

// Configurações do Sandbox PagSeguro (Simulação para Ambiente XAMPP/Online)
define('PAGSEGURO_ENV', 'sandbox');
define('PAGSEGURO_EMAIL', 'financeiro@masterschool.edu.br');
define('PAGSEGURO_TOKEN', 'SANDBOX_MASTER_SCHOOL_2026_TOKEN');
define('PAGSEGURO_PUBLIC_KEY', 'PUB_KEY_MASTER_SCHOOL_ERP');

/**
 * Funções Utilitárias Gerais
 */

function base_url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function format_currency($amount) {
    return 'R$ ' . number_format((float)$amount, 2, ',', '.');
}

function format_date($date, $format = 'd/m/Y') {
    if (!$date || $date === '0000-00-00') return '-';
    $dt = new DateTime($date);
    return $dt->format($format);
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function get_user_name() {
    return $_SESSION['user_name'] ?? 'Visitante';
}

/**
 * Grava evento no log de auditoria do sistema
 */
function log_system_action($pdo, $user_id, $action) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO system_logs (usuario_id, acao, ip_address, data_hora) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $action, $ip]);
    } catch (Exception $e) {
        // Silencia falha de log para não interromper fluxo principal em modo debug
    }
}
