<?php

require_once __DIR__ . '/../../src/helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // TODO: responder erro 405
    json_response(false, 'Método inválido.', null, 405);
}

session_start();

// TODO: limpar o array $_SESSION
$_SESSION = [];

// TODO: excluir o cookie de sessão quando ele estiver em uso
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $params['path'],
        'domain'   => $params['domain'],
        'secure'   => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?: 'Lax',
    ]);
}

// TODO: destruir os dados da sessão
session_destroy();

// TODO: responder que o logout foi concluído
json_response(true, 'Logout realizado com sucesso.');

