// chrispsicologo/js/esqueci-senha.js

document.addEventListener('DOMContentLoaded', function() {

    // Configurações (CHAVES CONFIRMADAS DO SEU PAINEL)
    const PUBLIC_KEY = 'dL0q3Ab4FKPWIXtg5'; 
    const SERVICE_ID = 'service_twmzfu6'; 
    const TEMPLATE_ID = 'template_bkeyh0k'; // ID do Template Responder
    emailjs.init(PUBLIC_KEY);

    const form = document.getElementById('form-recuperar-senha');
    const statusMessage = document.getElementById('status-mensagem-recuperacao');
    const submitButton = document.getElementById('btn-recuperar-senha');
    const emailInput = document.getElementById('email_recuperacao');

    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = emailInput.value;

        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = '';
        statusMessage.style.color = 'black';
        
        // --- CORREÇÃO APLICADA AQUI ---
        // Cria a URL de recuperação dinamicamente usando o domínio atual do site
        const recovery_link = `${window.location.origin}/esqueci-senha.html?email=${encodeURIComponent(email)}`;
        
        const templateParams = {
            email: email, 
            recovery_link: recovery_link, 
            from_email: 'jeanmarconascimentodarocha@gmail.com' 
        };

        emailjs.send(SERVICE_ID, TEMPLATE_ID, templateParams)
            .then(function(response) {
                console.log('SUCESSO!', response.status, response.text);
                
                statusMessage.textContent = 'Se o e-mail estiver cadastrado, um link de recuperação foi enviado.';
                statusMessage.style.color = 'green';
                form.reset();

            }, function(error) {
                console.error('FALHA...', error);
                
                statusMessage.textContent = 'Se o e-mail estiver cadastrado, um link de recuperação foi enviado.';
                statusMessage.style.color = 'green'; 
            })
            .finally(function() {
                submitButton.textContent = 'Enviar Link de Redefinição';
                submitButton.disabled = false;
            });
    });
});