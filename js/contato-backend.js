// js/contato-backend.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('backend-contact-form');
    const statusMessage = document.getElementById('status-mensagem');
    const submitButton = document.getElementById('btn-enviar-contato');

    if (!form) return;

    form.addEventListener('submit', async function(event) {
        event.preventDefault(); // Impede o envio tradicional

        // Pega os valores dos campos
        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const mensagem = document.getElementById('mensagem').value.trim();

        // Validação simples no frontend
        if (!nome || !email || !mensagem) {
            statusMessage.textContent = 'Por favor, preencha todos os campos obrigatórios.';
            statusMessage.style.color = 'red';
            return;
        }
        // Validação básica de email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            statusMessage.textContent = 'Por favor, insira um e-mail válido.';
            statusMessage.style.color = 'red';
            return;
        }


        // Desabilita o botão e mostra mensagem de envio
        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = ''; // Limpa mensagens anteriores

        // Prepara os dados para enviar (URLSearchParams é bom para application/x-www-form-urlencoded)
        const formData = new URLSearchParams();
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('mensagem', mensagem);

        try {
            // Envia a requisição para o backend
            const response = await fetch(form.action, { // form.action é '/backend/enviar-contato'
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });

            // Processa a resposta JSON do backend
            const result = await response.json();

            if (response.ok && result.success) {
                statusMessage.textContent = result.message || 'Mensagem enviada com sucesso!';
                statusMessage.style.color = 'green';
                form.reset(); // Limpa o formulário
            } else {
                // Se o backend retornou erro (status não ok ou success: false)
                throw new Error(result.message || `Erro ${response.status}: Não foi possível enviar a mensagem.`);
            }
        } catch (error) {
            console.error('Erro ao enviar formulário de contato:', error);
            statusMessage.textContent = error.message || 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
            statusMessage.style.color = 'red';
        } finally {
            // Reabilita o botão
            submitButton.textContent = 'Enviar Mensagem';
            submitButton.disabled = false;
        }
    });
});