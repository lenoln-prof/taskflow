<?php

require_once __DIR__ . '/../../src/helpers/response.php';
require_once __DIR__ . '/../../src/helpers/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/Project.php';

verificar_autenticacao();

$userId = (int) $_SESSION['user_id'];
$project = new Project($pdo);
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo === 'GET') {
        // TODO (Funcionalidade 2): listar os projetos

    } elseif ($metodo === 'POST') {
        $corpo = file_get_contents('php://input');

        try {
            $dados = json_decode($corpo, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            json_response(false, 'JSON inválido', null, 400);
        }

        if (!is_array($dados)) {
            json_response(false, 'O corpo deve ser um objeto JSON', null, 400);
        }

        $nome = trim($dados['nome']) ?? null;
        $descricao = trim($dados['descricao']) ?? null;
        $erros = [];

        // TODO: se $nome não for string, adicionar 'nome: deve ser texto' a $erros
        if (!is_string($nome)){
            $erros['nome'] = 'nome: deve ser texto';
        }
        // TODO: se for string, aplicar trim() e conferir de 1 a 150 caracteres
        //       usando mb_strlen($nome, 'UTF-8')
        $quantidadeCharNome = mb_strlen($nome, 'UTF-8');

        if ($quantidadeCharNome < 1 || $quantidadeCharNome > 150) {
            $erros['nome'] = 'nome: deve conter entre 1 e 150 caractéres.';
        }
        // TODO: $descricao deve ser null ou string; se for string, aplicar trim()
        //       (um texto vazio pode virar null)


        if ($erros) {
            json_response(false, 'Dados inválidos', $erros, 422);
        }

        $projeto = $project->criar($nome, $descricao, $userId);
        json_response(true, 'Projeto criado com sucesso', $projeto, 201);

    } else {
        header('Allow: GET, POST');
        json_response(false, 'Método não permitido', null, 405);
    }
} catch (PDOException | RuntimeException $e) {
    error_log($e->getMessage()); // o detalhe fica no servidor
    json_response(false, 'Erro interno do servidor', null, 500);
}
