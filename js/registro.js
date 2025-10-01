// chrispsicologo/js/registro.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-registro');
    const statusMessage = document.getElementById('status-mensagem-registro');
    const senha = document.getElementById('senha_usuario');
    const confirmaSenha = document.getElementById('confirma_senha');
    const submitButton = document.getElementById('btn-registro');

    if (!form) return;

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        statusMessage.textContent = '';
        
        // 1. Validação de Senha no Cliente
        if (senha.value !== confirmaSenha.value) {
            statusMessage.textContent = 'As senhas não coincidem!';
            statusMessage.style.color = 'red';
            return;
        }

        if (senha.value.length < 6) {
            statusMessage.textContent = 'A senha deve ter pelo menos 6 caracteres.';
            statusMessage.style.color = 'red';
            return;
        }

        submitButton.textContent = 'Processando...';
        submitButton.disabled = true;

        // 2. Coleta de Dados do Formulário (excluindo a confirmação de senha)
        const formData = new URLSearchParams();
        formData.append('nome_usuario', document.getElementById('nome_usuario').value);
        formData.append('email_usuario', document.getElementById('email_usuario').value);
        formData.append('senha_usuario', senha.value);
        // O campo 'tipo_usuario' é hardcoded como 'user' no HTML para registro público
        formData.append('tipo_usuario', 'user'); 

        // 3. Submissão AJAX para o Backend (Rota: POST /backend/usuario/salvar)
        try {
            // A rota de sucesso para o salvarUsuarios é configurada no PHP para retornar uma string de sucesso
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });

            // Como o controller no PHP ecoa uma string (não JSON), lemos como texto.
            const resultText = await response.text();
            
            // Verifica o status da resposta HTTP
            if (response.ok) {
                // Se a criação foi bem-sucedida (status 200 OK)
                statusMessage.textContent = 'Registro realizado com sucesso! Você pode fazer login.';
                statusMessage.style.color = 'green';
                form.reset(); // Limpa o formulário
                
                // Opcional: Redirecionar para o login
                setTimeout(() => {
                    window.location.href = 'index.html'; // Redireciona para a página inicial (onde está o modal de login)
                }, 2000);

            } else {
                // Se o servidor retornar um erro (e.g., 404, 500)
                statusMessage.textContent = 'Erro ao registrar. Tente um email diferente.';
                statusMessage.style.color = 'red';
                console.error('Erro de Servidor/Backend:', resultText);
            }
        } catch (error) {
            console.error('Erro de Rede:', error);
            statusMessage.textContent = 'Erro de conexão com o servidor. Verifique sua rede.';
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Registrar';
            submitButton.disabled = false;
        }
    });
});