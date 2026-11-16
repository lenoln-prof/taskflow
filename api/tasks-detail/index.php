<?php

require_once __DIR__ . '/../../src/helpers/response.php';
require_once __DIR__ . '/../../src/helpers/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/validate.php';
require_once __DIR__ . '/../../src/models/Task.php';

verificar_autenticacao();

$userId = (int) $_SESSION['user_id'];
$task = new Task($pdo);
$metodo = $_SERVER['REQUEST_METHOD'];

$permitidos = ['GET', 'PUT', 'PATCH', 'DELETE'];
if (!in_array($metodo, $permitidos, true)) {
    header('Allow: ' . implode(', ', $permitidos));
    json_response(false, 'Método não permitido', null, 405);
}

$id = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
if ($id === false) {
    json_response(false, 'id inválido', null, 400);
}

try {
    // TODO: encaminhar GET, PATCH, PUT e DELETE (Funcionalidades 3 a 6).
    if ($metodo === 'GET') {
        $tarefa = $task->buscarPorId($id, $userId);
        // TODO: responder 404 se $tarefa for null.
        if (!$tarefa) {
            json_response(false, 'Tarefa não encontrada', null, 404);
        }
        // TODO: responder 200 com os dados se encontrada.
        json_response(true, 'Tarefa encontrada', $tarefa);
    } elseif($metodo === 'PATCH') {
        $dados = ler_json_corpo();

        // TODO: responder 400 se $dados for null.
        if (!$dados) {
            json_response(false, 'Corpo JSON inválido', null, 400);
        }

        $status = $dados['status'] ?? null;
        // TODO: status deve ser string e estar em Task::STATUS (400 se não).
        if (!is_string($status) || !in_array($status, Task::STATUS, true)) {
            json_response(false, 'Status inválido', null, 400);
        }

        // TODO: chamar atualizarStatus($id, $userId, $status).
        $statusAtualizado = $task->atualizarStatus($id, $userId, $status);

        // TODO: responder 200 com a tarefa, ou 404 se vier null.
        if (!$statusAtualizado) {
            json_response(false, 'Tarefa não encontrada.', null, 404);
        }

        json_response(true, 'Status atualizado com sucesso!', $statusAtualizado);
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    json_response(false, 'Erro interno do servidor', null, 500);
}
