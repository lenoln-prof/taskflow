configurarToggleSenha("login-toggle-senha", "login-senha");

document.getElementById("form-login").addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const email = document.getElementById("login-email").value;
    const senha = document.getElementById("login-senha").value;
    const mensagem = document.getElementById("login-mensagem");

    try {
        const resposta = await fetch("/api/auth/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, senha }),
        });

        const resultado = await parseRespostaJson(resposta);

        if (!resultado) {
            exibirMensagem(mensagem, "Erro inesperado no servidor.", "erro");
            return;
        }

        if (!resposta.ok || !resultado.sucesso) {
            exibirMensagem(mensagem, resultado.mensagem, "erro");
            return;
        }

        exibirMensagem(mensagem, resultado.mensagem, "sucesso");
        window.location.href = "../../projetos.html";
    } catch (erro) {
        console.error("Erro na requisição:", erro);
        exibirMensagem(mensagem, "Falha de conexão com o servidor.", "erro");
    }
});
