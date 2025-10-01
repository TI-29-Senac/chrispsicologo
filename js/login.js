// chrispsicologo/js/login.js

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('login-modal');
    const form = document.getElementById('login-form');
    const statusMessage = document.getElementById('login-status-message');

    // Função global para abrir o modal, chamada pelo link do menu
    window.abrirLoginModal = () => {
        modal.classList.add('open');
        // Fecha o menu lateral se estiver aberto (redundância para segurança)
        const menuLateral = document.getElementById('menu-lateral');
        const overlay = document.getElementById('fundo-escuro');
        if (menuLateral && menuLateral.classList.contains('aberto')) {
            menuLateral.classList.remove('aberto');
            overlay.classList.remove('ativo');
        }
    }

    // Função global para fechar o modal (chamada pelo 'X' e pelo clique fora)
    window.fecharLoginModal = () => {
        modal.classList.remove('open');
        statusMessage.textContent = ''; // Limpa mensagem de status
        form.reset();
    }

    // Fecha ao clicar fora do modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharLoginModal();
        }
    });

    // Processa a submissão do formulário via Fetch API (AJAX)
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('login-email').value;
            const senha = document.getElementById('login-senha').value;
            const submitButton = form.querySelector('button[type="submit"]');

            submitButton.textContent = 'Verificando...';
            submitButton.disabled = true;
            statusMessage.textContent = '';
            statusMessage.style.color = 'black';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // Envia os dados no formato esperado pelo PHP ($_POST)
                    body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
                });

                const result = await response.json();

                if (response.ok) {
                    statusMessage.textContent = result.message || 'Login bem-sucedido! Redirecionando...';
                    statusMessage.style.color = 'green';
                    
                    // Redireciona após o sucesso
                    setTimeout(() => {
                         window.location.href = '/backend/dashboard'; // Rota de destino após o login
                    }, 1500);

                } else {
                    statusMessage.textContent = result.message || 'Erro de autenticação. Tente novamente.';
                    statusMessage.style.color = 'red';
                }
            } catch (error) {
                console.error('Erro de rede/servidor:', error);
                statusMessage.textContent = 'Erro ao conectar com o servidor.';
                statusMessage.style.color = 'red';
            } finally {
                submitButton.textContent = 'Entrar';
                submitButton.disabled = false;
            }
        });
    }
});

    // Funções de redirecionamento para Registro e Esqueci a Senha
    window.redirecionarRegistro = () => {
        // Linkando para uma página de registro separada
        window.location.href = 'registro.html'; 
    }

    window.redirecionarEsqueciSenha = () => {
        // Linkando para uma página de recuperação de senha separada
        window.location.href = 'esqueci-senha.html';
    }