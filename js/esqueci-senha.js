// chrispsicologo/js/esqueci-senha.js

document.addEventListener('DOMContentLoaded', function() {

    // Configurações (CHAVES CONFIRMADAS DO SEU PAINEL)
    const PUBLIC_KEY = 'dL0q3Ab4FKPWIXtg5'; 
    const SERVICE_ID = 'service_twmzfu6'; 
    const TEMPLATE_ID = 'template_bkeyh0k'; // ID do Template Responder
    emailjs.init(PUBLIC_KEY);

    // ⚠️ RECUPERAÇÃO DAS VARIÁVEIS DE INTERFACE (ESTAVAM FALTANDO)
    const form = document.getElementById('form-recuperar-senha');
    const statusMessage = document.getElementById('status-mensagem-recuperacao');
    const submitButton = document.getElementById('btn-recuperar-senha');
    const emailInput = document.getElementById('email_recuperacao');

    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = emailInput.value;

        // 1. ATUALIZAÇÃO DO ESTADO DA UI
        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = '';
        statusMessage.style.color = 'black';
        
        // 2. Parâmetros do Template
        const templateParams = {
            // Variável ajustada para 'email' (conforme seu painel)
            email: email, 
            // URL ajustada
            recovery_link: 'http://localhost:9000/esqueci-senha.html?email=' + encodeURIComponent(email), 
            // from_email (Se for um e-mail não verificado no EmailJS, o envio falha aqui)
            from_email: 'jeanmarconascimentodarocha@gmail.com' 
        };

        // 3. ENVIO DO EMAIL COM TRATAMENTO DE PROMESSA (.then/.finally)
        emailjs.send(SERVICE_ID, TEMPLATE_ID, templateParams)
            .then(function(response) {
                console.log('SUCESSO!', response.status, response.text);
                
                // Mensagem de sucesso segura (não revela se o e-mail existe)
                statusMessage.textContent = 'Se o e-mail estiver cadastrado, um link de recuperação foi enviado.';
                statusMessage.style.color = 'green';
                form.reset();

            }, function(error) {
                console.error('FALHA...', error);
                
                // Em caso de falha de envio, mantém a mensagem de sucesso segura
                statusMessage.textContent = 'Se o e-mail estiver cadastrado, um link de recuperação foi enviado.';
                statusMessage.style.color = 'green'; 
            })
            .finally(function() {
                // 4. RESTAURAÇÃO DO BOTÃO
                submitButton.textContent = 'Enviar Link de Redefinição';
                submitButton.disabled = false;
            });
    });
});