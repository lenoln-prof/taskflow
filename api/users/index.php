<?php

require_once  __DIR__ . '/../../src/helpers/response.php';
require_once  __DIR__ . '/../../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo !== 'GET') {
    json_response(false, 'Método não permitido', null, 405);
}

try {
    // TODO: preparar o $sql com o SELECT sem a coluna senha_hash
    $sql = "SELECT id, nome, email FROM users";
    $stmt = $pdo->prepare($sql);

    // TODO: executar a instrução
    $stmt->execute();
    // TODO: recuperar todas as linhas
    $usuarios = $stmt->fetchAll();
    // TODO: responder com os usuários utilizando a função json_response 
    json_response(true, 'Usuários encontrados', $usuarios, 200);
} catch (PDOException $e) {
    // TODO: responder com erro e status 500 com a função json_response
    json_response(false, 'Erro ao buscar usuários', null, 500);
}
