const API_BASE = "/api";
const LOGIN_URL = "/auth/login.html";
const areaProjetos = document.getElementById("area-projetos");

const STATUS_TEXTO = {
    ativo: "Em andamento",
    arquivado: "Arquivado",
};

function criarElemento(tag, classe, texto) {
    const el = document.createElement(tag);
    if (classe) el.className = classe;
    if (texto !== undefined) el.textContent = texto;
    return el;
}

function formatarData(dataIso) {
    const data = new Date(dataIso.replace(" ", "T"));
    return data.toLocaleDateString("pt-BR", { day: "2-digit", month: "short", year: "numeric" });
}

function criarLinhaProjeto(projeto) {
    const total = Number(projeto.total_tarefas) || 0;
    const concluidas = Number(projeto.tarefas_concluidas) || 0;
    const percentual = total === 0 ? 0 : Math.round((concluidas / total) * 100);

    const linha = criarElemento("div", "project-row");

    // Coluna nome
    const colNome = criarElemento("div", "col-nome");
    colNome.append(
        criarElemento("strong", null, projeto.nome),
        criarElemento("small", null, `Criado em ${formatarData(projeto.created_at)}`)
    );

    // Coluna status
    const colStatus = criarElemento("div", "col-status");
    const pill = criarElemento("span", "pill pill-status");
    pill.append(
        criarElemento("span", "status-dot"),
        STATUS_TEXTO[projeto.status] ?? projeto.status
    );
    colStatus.append(pill);

    // Coluna progresso
    const colProgresso = criarElemento("div", "col-progresso");
    const barraExterna = criarElemento("div", "progress");
    const barraInterna = criarElemento("div", "progress-bar");
    barraInterna.style.width = `${percentual}%`;
    barraExterna.append(barraInterna);
    colProgresso.append(barraExterna, criarElemento("span", "progress-value", `${percentual}%`));

    // Coluna tarefas
    const colTarefas = criarElemento("div", "col-tarefas", `${concluidas}/${total}`);

    linha.append(colNome, colStatus, colProgresso, colTarefas);
    return linha;
}

async function carregarProjetos() {
    areaProjetos.style.fontFamily = "Hanken Grotesk";
    areaProjetos.textContent = "Carregando projetos...";

    try {
        const resposta = await fetch(`${API_BASE}/projects/index.php`);

        if (resposta.status === 401) {
            window.location.href = LOGIN_URL;
            return;
        }

        const resultado = await resposta.json();

        if (!resposta.ok || !resultado.sucesso) {
            throw new Error(resultado.mensagem || "Erro ao buscar projetos");
        }

        areaProjetos.replaceChildren();

        if (resultado.dados.length === 0) {
            areaProjetos.textContent = "Nenhum projeto cadastrado.";
            return;
        }

        areaProjetos.append(...resultado.dados.map(criarLinhaProjeto));
    } catch (erro) {
        console.error("Erro ao carregar projetos:", erro);
        areaProjetos.textContent = "Não foi possível carregar os projetos.";
    }
}

carregarProjetos();