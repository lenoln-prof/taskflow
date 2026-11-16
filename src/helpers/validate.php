<?php

function validar_cadastro(array $dados): array
{
    $erros = [];

    // TODO: validar nome obrigatório e remover espaços extras
    $nome = trim($dados['nome'] ?? '');
    if (empty($nome)) {
        $erros['nome'] = 'Nome é obrigatório.';
    }

    // TODO: validar e-mail com FILTER_VALIDATE_EMAIL
    $email = trim($dados['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'E-mail inválido.';
    }

    // TODO: validar a regra mínima de senha definida pela turma
    $senha = strlen($dados['senha']);
    if (empty($senha)) {
        $erros['senha'] = 'A senha é obrigatória.';
    }
    if ($senha < 6) {
        $erros['senha'] = 'A senha deve ter no mínimo 6 caractéres.';
    }
    return $erros;
}

function validar_login(array $dados): array
{
    $erros = [];
    // TODO: validar somente os campos necessários para o login
    // TODO: validar e-mail com FILTER_VALIDATE_EMAIL
    $email = trim($dados['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'E-mail inválido.';
    }

    // TODO: validar a regra mínima de senha definida pela turma
    $senha = strlen($dados['senha']);
    if (empty($senha) || $senha < 6) {
        $erros['senha'] = 'A senha deve ter no mínimo 6 caractéres.';
    }

    return $erros;
}

function ler_json_corpo(): ?array
{
    $dados = json_decode(file_get_contents('php://input'), true);
    if (!is_array($dados) || array_is_list($dados)) {
        return null; // JSON inválido, ou lista no lugar de objeto
    }
    return $dados;
}

function validar_data_iso(string $data): bool
{
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return $d !== false && $d->format('Y-m-d') === $data;
}

