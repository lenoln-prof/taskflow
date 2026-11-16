<?php

require_once __DIR__ . '/../../src/helpers/response.php';
require_once __DIR__ . '/../../src/helpers/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/Project.php';

verificar_autenticacao();

$userId = (int) $_SESSION['user_id'];
$projects = new Project($pdo);
$metodo = $_SERVER['REQUEST_METHOD'];

if (!in_array($metodo, ['GET', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, PUT, DELETE');
    json_response(false, 'Método não permitido', null, 405);
}

$id = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
if ($id === false) {
    json_response(false, 'id inválido ou ausente', null, 400);
}

try {
    if ($metodo === 'GET') {
        // TODO (Funcionalidade 3): buscar
        $projeto = $projects->buscarPorId($id, $userId);
        // TODO: se $projeto for null, responder 404 ('Projeto não encontrado')

        if (!$projeto) {
            json_response(false, 'Projeto não encontrado', null, 404);
        }
        // TODO: caso contrário, responder 200 com os dados
        json_response(true, 'Projeto encontrado', $projeto);

    } elseif ($metodo === 'PUT') {
        // TODO (Funcionalidade 4): atualizar


    } else {
        // TODO (Funcionalidade 5): excluir

    }
} catch (PDOException | RuntimeException $e) {
    // TODO: na Funcionalidade 5, tratar o SQLSTATE 23503 como 409
    error_log($e->getMessage());
    json_response(false, 'Erro interno do servidor', null, 500);
}
