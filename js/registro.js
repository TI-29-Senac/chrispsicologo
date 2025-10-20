// chrispsicologo/js/registro.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-registro');
    const statusMessage = document.getElementById('status-mensagem-registro');
    const senha = document.getElementById('senha_usuario');
    const confirmaSenha = document.getElementById('confirma_senha');
    const submitButton = document.getElementById('btn-registro');

    if (!form) return;

    // Tenta exibir mensagens Flash que possam ter vindo do backend via redirecionamento
    // (Isso requer que a página registro.html consiga renderizar mensagens Flash,
    // o que não acontece por padrão em páginas HTML estáticas.
    // Uma solução seria passar a mensagem via query string no redirecionamento do PHP,
    // e o JS leria essa query string. Ex: header('Location: /registro.html?error=Email+em+uso'); )

    // Exemplo de como ler query string (adicionar isso se modificar o PHP para redirecionar com query string)
    /*
    const urlParams = new URLSearchParams(window.location.search);
    const errorMsg = urlParams.get('error');
    const successMsg = urlParams.get('success');
    if (errorMsg) {
        statusMessage.textContent = decodeURIComponent(errorMsg);
        statusMessage.style.color = 'red';
    } else if (successMsg) {
        statusMessage.textContent = decodeURIComponent(successMsg);
        statusMessage.style.color = 'green';
    }
    // Limpa a query string para não exibir a mensagem novamente no refresh
    if (window.history.replaceState) {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
    }
    */


    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        statusMessage.textContent = ''; // Limpa mensagens anteriores

        // Validações Frontend
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

        submitButton.textContent = 'A processar...';
        submitButton.disabled = true;

        // Prepara os dados do formulário
        const formData = new URLSearchParams();
        formData.append('nome_usuario', document.getElementById('nome_usuario').value);
        formData.append('email_usuario', document.getElementById('email_usuario').value);
        formData.append('senha_usuario', senha.value);
        // O tipo 'cliente' será definido no backend se não for especificado ou for 'user'
        // formData.append('tipo_usuario', 'cliente'); // Pode ser omitido

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
                // redirect: 'manual' // Evita que o fetch siga o redirect automaticamente
            });

            // Se o backend redirecionou (status 302, 301 etc.) E definiu uma mensagem Flash,
            // o utilizador será redirecionado pelo browser e verá a mensagem na próxima página.
            // O código abaixo só será executado se o backend NÃO redirecionar
            // (ex: retornar um erro 400 ou 500 diretamente, sem redirect com Flash).

            if (response.ok) {
                // Sucesso (improvável chegar aqui se o backend redireciona no sucesso)
                statusMessage.textContent = 'Registo bem-sucedido! A redirecionar...';
                statusMessage.style.color = 'green';
                form.reset();
                setTimeout(() => {
                     window.location.href = 'index.html'; // Redireciona para login/index
                }, 2000);

            } else {
                 // Tenta obter uma mensagem de erro do corpo da resposta, se houver
                 let resultText = 'Erro ao registar. Verifique os dados ou tente novamente.'; // Mensagem padrão
                 try {
                     // Se o backend retornar JSON em caso de erro direto (sem redirect)
                     // const resultJson = await response.json();
                     // resultText = resultJson.message || resultText;

                     // Se retornar texto simples
                     const text = await response.text();
                     if (text) resultText = text; // Usa o texto retornado se houver

                 } catch (e) {
                     // Ignora erros ao tentar ler o corpo da resposta
                 }

                statusMessage.textContent = resultText;
                statusMessage.style.color = 'red';
                console.error('Erro no registo:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Erro de Rede:', error);
            statusMessage.textContent = 'Erro de conexão. Verifique a sua rede e tente novamente.';
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Registar';
            submitButton.disabled = false;
        }
    });
});