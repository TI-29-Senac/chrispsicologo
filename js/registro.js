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

        const formData = new URLSearchParams();
        formData.append('nome_usuario', document.getElementById('nome_usuario').value);
        formData.append('email_usuario', document.getElementById('email_usuario').value);
        formData.append('senha_usuario', senha.value);

        formData.append('tipo_usuario', 'cliente'); // ou 'profissional' se houver opção no futuro

        try {

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });


            const resultText = await response.text();
            
            if (response.ok) {
                statusMessage.textContent = 'Registro realizado com sucesso! Você pode fazer login.';
                statusMessage.style.color = 'green';
                form.reset(); 
   
                setTimeout(() => {
                    window.location.href = 'index.html'; 
                }, 2000);

            } else {
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