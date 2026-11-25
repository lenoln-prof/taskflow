function configurarToggleSenha(botaoId, ...campoIds) {
    const botao = document.getElementById(botaoId);
    const campos = campoIds.map((id) => document.getElementById(id));

    botao.addEventListener("click", (evento) => {
        const mostrar = campos[0].type === "password";
        campos.forEach((campo) => { campo.type = mostrar ? "text" : "password"; });
        evento.target.textContent = mostrar ? "Ocultar" : "Mostrar";
    });
}

async function parseRespostaJson(resposta) {
    const textoResposta = await resposta.text();

    try {
        return JSON.parse(textoResposta);
    } catch (e) {
        console.error("O servidor não retornou JSON. Status:", resposta.status);
        console.error("Conteúdo recebido:", textoResposta);
        return null;
    }
}

function exibirMensagem(elemento, texto, tipo) {
    elemento.textContent = texto;
    elemento.className = `mensagem ${tipo}`;
}
