document.getElementById("logout").addEventListener("click", async (evento) => {
    evento.preventDefault();
    try {
        const resposta = await fetch("/api/auth/logout.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" }
        });

        window.location.href = "../../auth/login.html";
    } catch (erro) {
        console.error("Erro na requisição:", erro);
        mensagem.textContent = "Falha de conexão com o servidor.";
        mensagem.className = "mensagem erro";
    }
});