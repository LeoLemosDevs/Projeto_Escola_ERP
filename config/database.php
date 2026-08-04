<?php
/**
 * Master School ERP - Conexão Segura com Banco de Dados MySQL (PDO)
 * Compatível com XAMPP e servidores de produção online.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'master_school_erp');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function get_db_connection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Se o banco ainda não existir, tenta conectar apenas ao HOST (útil no primeiro acesso ou no install.php)
            try {
                $dsnHost = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                $pdoHost = new PDO($dsnHost, DB_USER, DB_PASS, $options);
                $pdoHost->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $ex) {
                die("<div style='font-family: sans-serif; padding: 20px; background: #fee2e2; border: 1px solid #ef4444; color: #7f1d1d; border-radius: 8px;'>
                        <h3>Erro de Conexão com o Banco de Dados XAMPP</h3>
                        <p><strong>Detalhes:</strong> " . htmlspecialchars($ex->getMessage()) . "</p>
                        <p>Verifique se o módulo <strong>MySQL</strong> está ativado em seu painel de controle XAMPP.</p>
                     </div>");
            }
        }
    }
    
    return $pdo;
}
