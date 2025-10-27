// chrispsicologo/js/esqueci-senha.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-recuperar-senha');
    const statusMessage = document.getElementById('status-mensagem-recuperacao');
    const submitButton = document.getElementById('btn-recuperar-senha');
    const emailInput = document.getElementById('email_recuperacao');

    if (!form) return;

    form.addEventListener('submit', async function(event) { // Adicionado async
        event.preventDefault();

        const email = emailInput.value;

        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = '';
        statusMessage.style.color = 'black';

        try {
            // --- MODIFICADO: Chama o backend via fetch ---
            const response = await fetch('/backend/recuperar-senha/solicitar', { // URL do novo endpoint
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', // Dados como formulário
                },
                body: `email=${encodeURIComponent(email)}` // Envia o email no corpo
            });

            const result = await response.json(); // Espera uma resposta JSON

            if (response.ok && result.success) {
                statusMessage.textContent = result.message; // Exibe a mensagem do backend
                statusMessage.style.color = 'green';
                form.reset();
            } else {
                // Se o backend retornou erro (4xx, 5xx) ou success: false
                throw new Error(result.message || `Erro ${response.status}`);
            }
            // --- FIM DA MODIFICAÇÃO ---

        } catch (error) {
            console.error('FALHA...', error);
            statusMessage.textContent = error.message || 'Ocorreu um erro. Tente novamente.';
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Enviar Link de Redefinição';
            submitButton.disabled = false;
        }
    });
});