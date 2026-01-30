// chrispsicologo/js/login.js

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('login-modal');
    const form = document.getElementById('login-form');
    const statusMessage = document.getElementById('login-status-message');

    // ... (abrirLoginModal, fecharLoginModal functions remain the same) ...
    window.abrirLoginModal = () => {
        modal.classList.add('open');
        // Fecha o menu lateral se estiver aberto (redundância para segurança)
        const menuLateral = document.getElementById('menu-lateral');
        const overlay = document.getElementById('fundo-escuro');
        if (menuLateral && menuLateral.classList.contains('aberto')) {
            menuLateral.classList.remove('aberto');
            if (overlay) overlay.classList.remove('ativo');
        }
    }

    window.fecharLoginModal = () => {
        modal.classList.remove('open');
        statusMessage.textContent = ''; // Limpa mensagem de status
        form.reset();
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharLoginModal();
        }
    });


    // Use event delegation for form submission to handle dynamic form injection
    document.body.addEventListener('submit', async (e) => {
        if (e.target.id === 'login-form') {
            e.preventDefault();
            const form = e.target;
            const emailInput = document.getElementById('login-email');
            const senhaInput = document.getElementById('login-senha');
            const submitButton = form.querySelector('button[type="submit"]');

            if (!emailInput || !senhaInput) {
                console.error('Login inputs not found!');
                return;
            }

            const email = emailInput.value;
            const senha = senhaInput.value;

            submitButton.textContent = 'Verificando...';
            submitButton.disabled = true;
            statusMessage.textContent = '';
            statusMessage.style.color = 'black';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email: email, senha: senha })
                });

                const result = await response.json();

                // Verifica response.ok E result.success (ou se existe token na resposta direta do Response::success)
                if (response.ok && (result.success || result.data?.token || result.token)) {
                    statusMessage.textContent = result.message || 'Login bem-sucedido! Redirecionando...';
                    statusMessage.style.color = 'green';

                    // Armazena Token e Nome
                    const token = result.data?.token || result.token;
                    const usuario = result.data?.usuario || result.usuario;

                    if (token) {
                        localStorage.setItem('auth_token', token);
                    }

                    if (usuario && usuario.nome_usuario) {
                        sessionStorage.setItem('welcomeUserName', usuario.nome_usuario);
                    }

                    // --- LÓGICA DE REDIRECIONAMENTO ATUALIZADA ---
                    setTimeout(() => {
                        // Decide o redirecionamento com base no userType recebido
                        const userType = usuario ? usuario.tipo_usuario : (result.userType || 'cliente');

                        if (['admin', 'recepcionista', 'profissional'].includes(userType)) {
                            window.location.href = '/backend/dashboard'; // Redireciona staff para o dashboard
                        } else {
                            window.location.href = '/minha-conta.html'; // Redireciona clientes (e outros) para o painel do cliente
                        }
                    }, 1500); // Espera 1.5 segundos

                } else {
                    // Se response.ok for false ou result.success for false
                    // Backend retorna 'error' key, não 'message' em Response::error
                    statusMessage.textContent = result.error || result.message || 'Erro de autenticação. Tente novamente.';
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
        }
    });

    // ... (redirecionarRegistro, redirecionarEsqueciSenha functions remain the same) ...
    window.redirecionarRegistro = () => {
        window.location.href = 'registro.html';
    }

    window.redirecionarEsqueciSenha = () => {
        window.location.href = 'esqueci-senha.html';
    }
});