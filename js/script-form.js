document.addEventListener('DOMContentLoaded', function() {
    const PUBLIC_KEY = 'dL0q3Ab4FKPWIXtg5';
    const SERVICE_ID = 'service_twmzfu6';
    const TEMPLATE_ID = 'template_yd4jov9';

    emailjs.init(PUBLIC_KEY);

    const form = document.getElementById('form-contato');
    const statusMessage = document.getElementById('status-mensagem');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = '';

        // Preparando os dados do template
        const templateParams = {
            from_name: document.getElementById('nome').value,
            from_email: document.getElementById('email').value,
            message: document.getElementById('mensagem').value
        };

        emailjs.send(SERVICE_ID, TEMPLATE_ID, templateParams)
            .then(function(response) { 
                console.log('SUCESSO!', response.status, response.text);
                statusMessage.textContent = 'E-mail enviado com sucesso!';
                statusMessage.style.color = 'green';
                form.reset();
            }, function(error) {
                console.log('FALHA...', error);
                statusMessage.textContent = 'Falha ao enviar o e-mail. Tente novamente mais tarde.';
                statusMessage.style.color = 'red';
            })
            .finally(function() {
                submitButton.textContent = 'Enviar Mensagem';
                submitButton.disabled = false;
            });
    });
});
