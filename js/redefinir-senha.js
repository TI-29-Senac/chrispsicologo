// chrispsicologo/js/redefinir-senha.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-redefinir-senha');
    const statusMessage = document.getElementById('status-mensagem-redefinicao');
    const submitButton = document.getElementById('btn-redefinir-senha');
    const novaSenhaInput = document.getElementById('nova_senha');
    const confirmaSenhaInput = document.getElementById('confirma_nova_senha');
    const tokenInput = document.getElementById('token_reset');
    const subtitulo = document.getElementById('subtitulo-redefinir');

    // 1. Pegar o token da URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (!token) {
        subtitulo.textContent = 'Token de redefinição inválido ou ausente.';
        subtitulo.style.color = 'red';
        form.style.display = 'none'; // Esconde o formulário se não houver token
        return;
    }

    // Coloca o token no campo hidden
    tokenInput.value = token;

    // 2. Opcional: Validar o token assim que a página carrega (melhora a UX)
    async function validarTokenInicial() {
        try {
            const response = await fetch(`/backend/recuperar-senha/validar/${encodeURIComponent(token)}`);
            if (!response.ok) {
                const result = await response.json().catch(() => ({})); // Tenta pegar a mensagem de erro
                throw new Error(result.message || 'Token inválido ou expirado.');
            }
            // Se o token for válido, não faz nada, permite que o usuário digite a senha
            console.log('Token válido.');

        } catch (error) {
            subtitulo.textContent = error.message || 'Não foi possível validar o token.';
            subtitulo.style.color = 'red';
            form.style.display = 'none'; // Esconde o formulário se o token for inválido
        }
    }
    validarTokenInicial(); // Chama a validação inicial

    // 3. Lógica de submissão do formulário
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        statusMessage.textContent = ''; // Limpa mensagens anteriores

        // Validações Frontend
        if (novaSenhaInput.value !== confirmaSenhaInput.value) {
            statusMessage.textContent = 'As senhas não coincidem!';
            statusMessage.style.color = 'red';
            return;
        }
        if (novaSenhaInput.value.length < 6) {
            statusMessage.textContent = 'A nova senha deve ter pelo menos 6 caracteres.';
            statusMessage.style.color = 'red';
            return;
        }

        submitButton.textContent = 'A guardar...';
        submitButton.disabled = true;

        // Prepara os dados do formulário
        const formData = new URLSearchParams();
        formData.append('token', tokenInput.value);
        formData.append('nova_senha', novaSenhaInput.value);

        try {
            const response = await fetch(form.action, { // A action é /backend/recuperar-senha/processar
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });

            const result = await response.json(); // Espera JSON

            if (response.ok && result.success) {
                statusMessage.textContent = result.message || 'Senha redefinida com sucesso! A redirecionar para o login...';
                statusMessage.style.color = 'green';
                form.reset();
                form.style.display = 'none'; // Esconde o formulário após sucesso
                setTimeout(() => {
                    window.location.href = 'index.html'; // Redireciona para login (na index)
                }, 3000);
            } else {
                throw new Error(result.message || `Erro ${response.status}`);
            }
        } catch (error) {
            console.error('Erro ao redefinir senha:', error);
            statusMessage.textContent = error.message || 'Ocorreu um erro. Verifique o link ou tente solicitar novamente.';
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Salvar Nova Senha';
            submitButton.disabled = false;
        }
    });
});