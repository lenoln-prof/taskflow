<?php

class Project
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criar(
        string $nome,
        ?string $descricao,
        int $userId
    ): array {
        // Os valores entram por parâmetros, nunca por concatenação.
        $sql = 'INSERT INTO projects (nome, descricao, user_id)
            VALUES (:nome, :descricao, :user_id)
            RETURNING id, nome, descricao, status,
                      user_id, created_at, updated_at';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome' => $nome,
            'descricao' => $descricao,
            'user_id' => $userId,
        ]);

        // RETURNING devolve a linha que acabou de ser inserida.
        $projeto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($projeto === false) {
            throw new RuntimeException('Projeto não retornado pelo INSERT.');
        }

        return $projeto;
    }

}
