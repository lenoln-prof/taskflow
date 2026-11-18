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
    } elseif ($metodo === 'PUT') {
        $dados = ler_json_corpo();
        // TODO: validar corpo e todos os campos (reaproveitar as
        //       regras do POST; status em Task::STATUS).
        if (!$dados) {
            json_response(false, 'Corpo JSON inválido', null, 400);
        }

        if ($dados === null) {
            json_response(false, 'Corpo JSON inválido', null, 400);
        }

        $projectId = filter_var(
            $dados['project_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        $titulo = $dados['titulo'] ?? null;
        $descricao = $dados['descricao'] ?? null;
        $status = $dados['status'] ?? null;
        $prioridade = $dados['prioridade'] ?? null;
        $dataInicio = $dados['data_inicio'] ?? null;
        $dataPrazo = $dados['data_prazo'] ?? null;

        $erros = [];

        if (!$projectId)
            $erros[] = 'ID do projeto inválido.';
        if (!is_string($titulo) || mb_strlen(trim($titulo)) < 1 || mb_strlen(trim($titulo)) > 200)
            $erros[] = 'titulo deve ser texto de 1 a 200 caracteres';
        if ($descricao !== null && !is_string($descricao))
            $erros[] = 'descricao deve ser texto ou null';
        if (!is_string($prioridade) || !in_array($prioridade, Task::PRIORIDADES, true))
            $erros[] = 'prioridade inválida';
        if (!is_string($status) || !in_array($status, Task::STATUS, true))
            $erros[] = 'status inválido';

        $datasValidas = true;
        $campos = ['data_inicio' => $dataInicio, 'data_prazo' => $dataPrazo];
        foreach ($campos as $campo => $valor) {
            if ($valor !== null && (!is_string($valor) || !validar_data_iso($valor))) {
                $erros[] = "$campo deve estar no formato AAAA-MM-DD";
                $datasValidas = false;
            }
        }

        if ($datasValidas && $dataInicio !== null && $dataPrazo !== null && $dataPrazo < $dataInicio)
            $erros[] = 'data_prazo não pode ser anterior a data_inicio';

        if ($erros !== []) json_response(false, 'Dados inválidos', $erros, 400);

        // TODO: chamar atualizar com valores já validados.
        $titulo = trim($titulo);
        if (!$descricao)
            $descricao = trim($descricao);

        $atualizado = $task->atualizar(
            $id, $userId, $titulo, $descricao,
            $status, $prioridade, $dataInicio, $dataPrazo
        );

        // TODO: responder 200 ou 404 conforme o resultado.
        if (!$atualizado) {
            json_response(false, 'Tarefa não encontrada.', null, 404);
        }

        json_response(true, 'Tarefa atualizada com sucesso!', $atualizado);

    }


} catch (PDOException $e) {
    error_log($e->getMessage());
    json_response(false, 'Erro interno do servidor', null, 500);
}
