document.addEventListener('DOMContentLoaded', function() {
    const PUBLIC_KEY = 'dL0q3Ab4FKPWIXtg5'
    const YOUR_SERVICE_I = 'service_twmzfu6'
    const YOUR_TEMPLATE_ID = 'template_yd4jov9';
    emailjs.init(PUBLIC_KEY);
    document.getElementById('form-contato').addEventListener('submit', function(event) {
        event.preventDefault();
        const statusMessage = document.getElementById('status-mensagem');
        const submitButton = this.querySelector('button[type="submit"]');

        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;
        statusMessage.textContent = '';

        const template = {
            from_name: document.getElementById('nome').value,
            from_email: document.getElementById('email').value,
            message: document.getElementById('mensagem').value
        };

        emailjs.send("service_twmzfu6","template_bkeyh0k",{
            email: document.getElementById('email').value,
            name: document.getElementById('nome').value,
            message: document.getElementById('mensagem').value
            });
            
        emailjs.send(YOUR_SERVICE_I, YOUR_TEMPLATE_ID, template)
            .then(function(response) { 
                console.log('SUCESSO!', response.status, response.text);
                statusMessage.textContent = 'E-mail enviado com sucesso!';
                statusMessage.style.color = 'green';
                
                document.getElementById('form-contato').reset();


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