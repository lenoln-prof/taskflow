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

    public function listar(int $userId, array $filtros = []): array
    {
        $sql = 'SELECT t.id, t.titulo, t.descricao, t.status, t.prioridade,
                   t.data_inicio, t.data_prazo, t.project_id,
                   p.nome AS projeto_nome, t.created_at, t.updated_at
            FROM tasks t
            INNER JOIN projects p ON p.id = t.project_id
            WHERE p.user_id = :user_id';
        $params = ['user_id' => $userId];

        if (isset($filtros['project_id'])) {
            $sql .= ' AND t.project_id = :project_id';
            $params['project_id'] = $filtros['project_id'];
        }
        if (isset($filtros['status'])) {
            $sql .= ' AND t.status = :status';
            $params['status'] = $filtros['status'];
        }
        if (isset($filtros['prioridade'])) {
            $sql .= ' AND t.prioridade = :prioridade';
            $params['prioridade'] = $filtros['prioridade'];
        }

        $sql .= ' ORDER BY t.data_prazo ASC NULLS LAST, t.id DESC
              LIMIT :limite';
        $params['limite'] = $filtros['limite'] ?? 50;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $nome => $valor) {
            $stmt->bindValue(
                ':' . $nome,
                $valor,
                is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id, int $userId): ?array
    {
        $sql = 'SELECT t.id, t.titulo, t.descricao, t.status, t.prioridade,
                   t.data_inicio, t.data_prazo, t.project_id,
                   p.nome AS projeto_nome, t.created_at, t.updated_at
            FROM tasks t
            INNER JOIN projects p ON p.id = t.project_id
            WHERE t.id = :id AND p.user_id = :user_id';

        // TODO: preparar SQL e executar os dois parâmetros.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        // TODO: obter uma linha usando fetch(PDO::FETCH_ASSOC).
        $tarefa = $stmt->fetch(PDO::FETCH_ASSOC);

        // TODO: retornar null se fetch() devolver false.
        return $tarefa === false ? null : $tarefa;
    }

    public function atualizarStatus(
        int $id,
        int $userId,
        string $status
    ): ?array {
        $sql = 'UPDATE tasks
            SET status = :status, updated_at = NOW()
            WHERE id = :id
              AND project_id IN (
                  SELECT id FROM projects WHERE user_id = :user_id
              )
            RETURNING id, titulo, status, prioridade,
                      project_id, updated_at';

        // TODO: preparar e executar os três parâmetros.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'id' => $id,
            'user_id' => $userId
        ]);

        $statusAtualizado = $stmt->fetch(PDO::FETCH_ASSOC);

        // TODO: retornar a linha alterada ou null.
        return $statusAtualizado === false ? null : $statusAtualizado;
    }

    public function atualizar(
        int $id,
        int $userId,
        string $titulo,
        ?string $descricao,
        string $status,
        string $prioridade,
        ?string $dataInicio,
        ?string $dataPrazo
    ): ?array {
        // TODO: UPDATE tasks SET titulo, descricao, status,
        //       prioridade, data_inicio, data_prazo e
        //       updated_at = NOW().
        // TODO: WHERE id = :id AND project_id IN (subconsulta do dono),
        //       igual ao atualizarStatus().
        // TODO: RETURNING das colunas da tarefa.
        $sql = 'UPDATE tasks
            SET titulo = :titulo, descricao = :descricao,
                status = :status, prioridade = :prioridade, 
                data_inicio = :data_inicio, data_prazo = :data_prazo, updated_at = NOW()
            WHERE id = :id
              AND project_id IN (
                  SELECT id FROM projects WHERE user_id = :user_id
              )
            RETURNING id, titulo, descricao, status, prioridade,
                      data_inicio, data_prazo, project_id, user_id, updated_at';

        // TODO: preparar, executar e retornar a linha ou null.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'descricao' => $descricao,
            'status' => $status,
            'prioridade' => $prioridade,
            'data_inicio' => $dataInicio,
            'data_prazo' => $dataPrazo,
            'id' => $id,
            'user_id' => $userId
        ]);

        $atualizado = $stmt->fetch(PDO::FETCH_ASSOC);

        // TODO: retornar a linha alterada ou null.
        return $atualizado === false ? null : $atualizado;
    }

    public function deletar(int $id, int $userId): bool
    {
        $sql = 'DELETE FROM tasks
            WHERE id = :id
              AND project_id IN (
                  SELECT id FROM projects WHERE user_id = :user_id
              )
            RETURNING id';
        // TODO: preparar e executar os dois parâmetros.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        // TODO: retornar true se houver linha em fetch().
        // TODO: retornar false se não houver linha.
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

}
