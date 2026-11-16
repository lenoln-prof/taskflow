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

    public function listar(int $userId): array
    {
        // O filtro restringe o resultado ao dono autenticado.
        $sql = 'SELECT id, nome, descricao, status,
                   user_id, created_at, updated_at
            FROM projects
            WHERE user_id = :user_id
            ORDER BY created_at DESC, id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id, int $userId): ?array
    {
        $sql = 'SELECT id, nome, descricao, status,
                   user_id, created_at, updated_at
            FROM projects
            WHERE id = :id AND user_id = :user_id';

        // TODO: preparar o SQL e executar com os dois parâmetros
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        // TODO: obter uma linha com fetch(PDO::FETCH_ASSOC)
        $projeto = $stmt->fetch(PDO::FETCH_ASSOC);

        // TODO: retornar null quando fetch() devolver false
        return $projeto === false ? null : $projeto;
    }
}
