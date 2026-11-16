<?php
class Task
{
    public const STATUS = ['pendente', 'em_andamento', 'concluido'];
    public const PRIORIDADES = ['baixa', 'media', 'alta'];

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // O projeto existe E é do usuário autenticado?
    private function projetoPertenceAoUsuario(
        int $projectId,
        int $userId
    ): bool {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM projects
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute(['id' => $projectId, 'user_id' => $userId]);
        return $stmt->fetchColumn() !== false;
    }

    // Inserir criar() e listar() dentro desta classe.
    public function criar(
        int $projectId,
        string $titulo,
        ?string $descricao,
        string $prioridade,
        ?string $dataInicio,
        ?string $dataPrazo,
        int $userId
    ): ?array {
        // Só cria tarefa em projeto do próprio usuário.
        if (!$this->projetoPertenceAoUsuario($projectId, $userId)) {
            return null;
        }

        $sql = 'INSERT INTO tasks
                (titulo, descricao, prioridade, data_inicio,
                 data_prazo, project_id, user_id)
            VALUES
                (:titulo, :descricao, :prioridade, :data_inicio,
                 :data_prazo, :project_id, :user_id)
            RETURNING id, titulo, descricao, status, prioridade,
                      data_inicio, data_prazo, project_id, user_id,
                      created_at, updated_at';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'descricao' => $descricao,
            'prioridade' => $prioridade,
            'data_inicio' => $dataInicio,
            'data_prazo' => $dataPrazo,
            'project_id' => $projectId,
            'user_id' => $userId,
        ]);

        // RETURNING devolve a linha que acabou de ser inserida.
        $tarefa = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tarefa === false) {
            throw new RuntimeException('Tarefa não retornada.');
        }
        return $tarefa;
    }

    // Os demais métodos serão desenvolvidos depois.
}
