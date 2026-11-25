<?php

require_once __DIR__ . '/../../src/helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Método inválido.', null, 405);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    json_response(false, 'Não autenticado.', null, 401);
}

json_response(true, 'Usuário autenticado.', [
    'id' => (int) $_SESSION['user_id'],
    'nome' => $_SESSION['user_nome'],
]);
