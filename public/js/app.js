async function redirecionarSeAutenticado(destino = "../projetos.html") {
    try {
        const resposta = await fetch("/api/auth/me.php");

        if (resposta.ok) {
            window.location.href = destino;
        }
    } catch (erro) {
        console.error("Erro ao verificar sessão:", erro);
    }
}

redirecionarSeAutenticado();
