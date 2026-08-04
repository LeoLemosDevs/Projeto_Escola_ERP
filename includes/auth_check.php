<?php
/**
 * Master School ERP - Controle de Acesso e Permissões (RBAC)
 * Garante que apenas usuários autenticados no perfil autorizado acessem o módulo.
 */

require_once __DIR__ . '/../config/config.php';

function require_role($allowedRoles = []) {
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }

    if (!is_logged_in()) {
        redirect(base_url('login.php'));
    }

    $currentRole = get_user_role();

    if (!in_array($currentRole, $allowedRoles, true)) {
        // Se estiver logado em outra função, redireciona ao painel correto
        if ($currentRole === 'aluno') redirect(base_url('aluno/index.php'));
        if ($currentRole === 'professor') redirect(base_url('professor/index.php'));
        if ($currentRole === 'admin') redirect(base_url('admin/index.php'));
        
        // Em último caso, encerra e redireciona ao login
        redirect(base_url('login.php'));
    }
}
