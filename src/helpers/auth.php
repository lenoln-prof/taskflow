<?php

require_once __DIR__ . '/response.php';

function verificar_autenticacao(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // TODO: iniciar a sessão
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        // TODO: responder erro 401 com json_response
        json_response(false, 'Acesso não autorizado.', null, 401);
    }
}
