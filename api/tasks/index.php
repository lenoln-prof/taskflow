<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/response.php';
require_once __DIR__ . '/../../src/helpers/auth.php';
require_once __DIR__ . '/../../src/helpers/validate.php';
require_once __DIR__ . '/../../src/models/Task.php';

verificar_autenticacao();
$userId = (int) $_SESSION['user_id'];
$task = new Task($pdo);
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo === 'GET') {
        // TODO: Funcionalidade 2 (listar).
        $filtros = [];
        $projectGet = $_GET['project_id'];
        $statusGet = $_GET['status'];
        $prioridadeGet = $_GET['prioridade'];
        $limiteGet = $_GET['limite'];

        if (isset($projectGet)) {
            $projectId = filter_var(
                $projectGet,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );
            if ($projectId === false) {
                json_response(false, 'project_id inválido', null, 400);
            }
            $filtros['project_id'] = $projectId;
        }

        // TODO: status deve estar em Task::STATUS (in_array estrito).
        if (isset($statusGet)) {
            if (!in_array($statusGet, Task::STATUS, true)) {
                json_response(false, 'status inválido', null, 400);
            }
            $filtros['status'] = $statusGet;
        }

        // TODO: prioridade deve estar em Task::PRIORIDADES.
        if (isset($prioridadeGet)) {
            if (!in_array($prioridadeGet, Task::PRIORIDADES, true)) {
                json_response(false, 'prioridade inválida', null, 400);
            }
            $filtros['prioridade'] = $prioridadeGet;
        }

        // TODO: limite: inteiro entre 1 e 100 (min_range e max_range).
        if (isset($limiteGet)) {
            $limite = filter_var(
                $limiteGet,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 100]]
            );
            if ($limite === false) {
                json_response(false, 'limite deve ser inteiro de 1 a 100', null, 400);
            }
            $filtros['limite'] = $limite;
        }

        $tarefas = $task->listar($userId, $filtros);
        json_response(true, 'Tarefas listadas', $tarefas);

    } elseif ($metodo === 'POST') {
        $dados = ler_json_corpo();
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
        $prioridade = $dados['prioridade'] ?? 'media';
        $dataInicio = $dados['data_inicio'] ?? null;
        $dataPrazo = $dados['data_prazo'] ?? null;

        $erros = [];

        // TODO: project_id === false: acrescentar mensagem em $erros.
        if (!$projectId) $erros[] = 'ID do projeto inválido.';

        // TODO: titulo deve ser string, de 1 a 200 caracteres após trim().
        if (!is_string($titulo) || mb_strlen(trim($titulo)) < 1 || mb_strlen(trim($titulo)) > 200) {
            $erros[] = 'titulo deve ser texto de 1 a 200 caracteres';
        }

        // TODO: descricao deve ser string ou null.
        if ($descricao !== null && !is_string($descricao)) {
            $erros[] = 'descricao deve ser texto ou null';
        }

        // TODO: prioridade em Task::PRIORIDADES (in_array estrito).
        if (!is_string($prioridade) || !in_array($prioridade, Task::PRIORIDADES, true)) {
            $erros[] = 'prioridade inválida';
        }

        // TODO: datas: null ou string aceita por validar_data_iso().
        $datasValidas = true;
        $campos = ['data_inicio' => $dataInicio, 'data_prazo' => $dataPrazo];
        foreach ($campos as $campo => $valor) {
            if ($valor !== null && (!is_string($valor) || !validar_data_iso($valor))) {
                $erros[] = "$campo deve estar no formato AAAA-MM-DD";
                $datasValidas = false;
            }
        }

        // TODO: com as duas datas, prazo não pode ser anterior ao início.
        if ($datasValidas && $dataInicio !== null && $dataPrazo !== null && $dataPrazo < $dataInicio) {
            $erros[] = 'data_prazo não pode ser anterior a data_inicio';
        }

        if ($erros !== []) {
            json_response(false, 'Dados inválidos', $erros, 400);
        }

        // TODO: aplicar trim() em titulo e descricao (se não for null).
        $titulo = trim($titulo);
        if ($descricao !== null) {
            $descricao = trim($descricao);
        }

        // TODO: chamar $model->criar(...) com os valores validados.
        $tarefa = $task->criar(
            $projectId,
            $titulo,
            $descricao,
            $prioridade,
            $dataInicio,
            $dataPrazo,
            $userId
        );

        // TODO: $tarefa === null: responder 404 "Projeto não encontrado".
        if (!$tarefa) {
            json_response(false, 'Projeto não encontrado', null, 404);
        }

        // TODO: responder 201 com json_response(true, 'Tarefa criada', ...).
        json_response(true, 'Tarefa criada', $tarefa, 201);

    } else {
        header('Allow: GET, POST');
        json_response(false, 'Método não permitido', null, 405);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    json_response(false, 'Erro interno do servidor', null, 500);
}
