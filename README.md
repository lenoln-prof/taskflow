# 2.2 Estrutura de Pastas

taskflow/
│
├── config/
│   ├── database.php        ← configuração da conexão PDO
│   └── config.php          ← constantes globais (versão, debug, etc.)
│
├── src/
│   ├── models/
│   │   ├── User.php        ← operações de usuário no banco
│   │   ├── Project.php     ← operações de projeto no banco
│   │   └── Task.php        ← operações de tarefa no banco
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   └── TaskController.php
│   │
│   └── helpers/
│       ├── response.php    ← funções para retornar JSON padronizado
│       ├── auth.php        ← funções de verificação de sessão
│       └── validate.php    ← funções de validação de dados
│
├── api/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── projects/
│   │   └── index.php       ← GET (listar), POST (criar)
│   ├── projects-detail/
│   │   └── index.php       ← GET (detalhe), PUT (editar), DELETE (remover)
│   ├── tasks/
│   │   └── index.php
│   └── tasks-detail/
│       └── index.php
│
├── public/
│   ├── index.html          ← página inicial (login/dashboard)
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js          ← fetch API básico
│
├── sql/
│   └── schema.sql          ← scripts de criação do banco
│
├── .gitignore
└── README.md
