<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/response.php';
require_once __DIR__ . '/../../src/helpers/validate.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // TODO: responder erro 405
    json_response(false, 'Método inválido.', null, 405);
}

$dados = json_decode(file_get_contents('php://input'), true);

try {
    // TODO: validar os dados do login
    $erros = validar_login($dados);

    if ($erros) {
        json_response(false, 'Dados inválidos.', $erros, 422);
    }

    $email = trim($dados['email']);
    $senha = $dados['senha'];

    // TODO: buscar o usuário pelo e-mail
    $stmt = $pdo->prepare(
        'SELECT id, nome, email, senha_hash FROM users WHERE email = :email'
    );
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch();

    // TODO: verificar a senha com password_verify
    if (!$user || !password_verify($senha, $user['senha_hash'])){
        json_response(false, "Email ou senha inválidos.", null, 401);
    }

    // TODO: salvar usuario_id e usuario_nome em $_SESSION
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nome'] = $user['nome'];

    // TODO: responder sucesso sem retornar senha_hash
    json_response(true, 'Login realizado com sucesso.', [
            'id' => (int) $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email']
        ]);

} catch (PDOException $e) {
    // TODO: responder erro 500 sem expor detalhes i
    json_response(false, 'Erro interno do servidor.', null, 500);
}
