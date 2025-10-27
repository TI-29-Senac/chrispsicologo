// js/minha-conta.js

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('minha-conta-container');
    
    // Verifica se o usuário está logado (usando a mesma lógica do site)
    const userName = sessionStorage.getItem('welcomeUserName');

    if (!userName) {
        // Exibe mensagem se não estiver logado e oferece botão para abrir modal
        container.innerHTML = `<div class="formulario-registro-container">
                                  <h3>Acesso Necessário</h3>
                                  <p class="subtitulo-registro">Você precisa estar logado para acessar esta página.</p>
                                  <button onclick="abrirLoginModal();" class="btn-login-agora">Fazer Login Agora</button>
                                </div>`;
        return;
    }

    async function carregarDadosDoPerfil() {
        container.innerHTML = `<p style="text-align: center; color: #5D6D68; font-family: 'Questrial', sans-serif; padding-top: 50px;">Carregando dados de ${userName}...</p>`;

        try {
            // Chama o endpoint API para obter os dados do usuário
            const response = await fetch('/backend/api/cliente/meu-perfil');
            const result = await response.json();

            if (response.ok && result.success) {
                renderizarInterface(result.data);
            } else {
                throw new Error(result.message || 'Falha ao carregar dados do perfil.');
            }
        } catch (error) {
            console.error('Erro ao carregar perfil:', error);
            container.innerHTML = `<div class="formulario-registro-container">
                                      <h3>Erro de Acesso</h3>
                                      <p class="subtitulo-registro" style="color: red;">${error.message || 'Não foi possível carregar o perfil.'}</p>
                                      <a href="#" onclick="performLogout(event);" class="btn-login-agora" style="background-color: #A3B8A1;">Sair e Tentar Novamente</a>
                                  </div>`;
        }
    }

    function renderizarInterface(usuario) {
        // ... (HTML de navegação e seção-perfil permanecem iguais) ...
        const conteudoHTML = `
            <div class="formulario-registro-container minha-conta-card">
                <h3>Minha Conta - ${usuario.nome_usuario.split(' ')[0]}</h3>
                <p class="subtitulo-registro">Gerencie seu perfil e agendamentos.</p>

                <div class="minha-conta-nav">
                    <button class="nav-btn active" data-secao="perfil">Meu Perfil</button>
                    <button class="nav-btn" data-secao="agendamentos">Meus Agendamentos</button>
                </div>
                <hr style="border-top: 1px solid #d6e3d6; margin: 20px 0;">

                <div id="secao-perfil" class="secao-conteudo">
                    <form id="form-atualizar-perfil" action="/backend/api/cliente/atualizar-perfil" method="POST">
                        
                        <label for="nome_usuario">Nome Completo</label>
                        <input type="text" id="nome_usuario" name="nome_usuario" value="${usuario.nome_usuario || ''}" required>
                        
                        <label for="email_usuario">E-mail</label>
                        <input type="email" id="email_usuario" name="email_usuario" value="${usuario.email_usuario || ''}" required>

                        <label for="cpf">CPF (Opcional)</label>
                        <input type="text" id="cpf" name="cpf" value="${usuario.cpf || ''}">

                        <label for="senha_usuario">Nova Senha (Deixe em branco para não alterar)</label>
                        <input type="password" id="senha_usuario" name="senha_usuario" placeholder="Mínimo 6 caracteres">

                        <button type="submit" id="btn-atualizar-perfil">Atualizar Perfil</button>
                        <p id="status-mensagem-perfil" style="margin-top: 15px; text-align: center;"></p>
                    </form>
                </div>

                <div id="secao-agendamentos" class="secao-conteudo hidden">
                     <div id="agendamentos-lista">
                         <p style="text-align: center; color: #5D6D68;">Clique na aba "Meus Agendamentos" para carregar.</p>
                     </div>
                </div>

                <div style="margin-top: 30px; text-align: center;">
                    <a href="#" onclick="performLogout(event);" style="color: red; text-decoration: underline;">Sair da conta</a>
                </div>
            </div>
        `;
        container.innerHTML = conteudoHTML;

        // Adiciona listeners aos botões de navegação
        document.querySelectorAll('.minha-conta-nav .nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                const secao = this.dataset.secao;
                
                // 1. Remove 'active' e 'hidden' de todas as abas
                document.querySelectorAll('.minha-conta-nav .nav-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.secao-conteudo').forEach(s => s.classList.add('hidden'));

                // 2. Ativa a aba clicada e exibe o conteúdo correspondente
                this.classList.add('active');
                document.getElementById(`secao-${secao}`).classList.remove('hidden');
                
                // 3. Se for a seção de agendamentos, carrega os dados
                if (secao === 'agendamentos') {
                    renderizarAgendamentos();
                }
            });
        });

        // Adiciona listener ao formulário de atualização de perfil
        document.getElementById('form-atualizar-perfil').addEventListener('submit', submeterAtualizacaoPerfil);
    }
    
    // NOVO MÉTODO PARA BUSCAR E RENDERIZAR AGENDAMENTOS
    async function renderizarAgendamentos() {
        const agendamentosLista = document.getElementById('agendamentos-lista');
        agendamentosLista.innerHTML = '<p style="text-align: center; color: #5D6D68;">Buscando seus agendamentos no sistema...</p>';

        try {
            const response = await fetch('/backend/api/cliente/meus-agendamentos');
            const result = await response.json();

            if (response.ok && result.success && result.agendamentos.length > 0) {
                agendamentosLista.innerHTML = '';
                
                result.agendamentos.forEach(agendamento => {
                    // Formata a data (de YYYY-MM-DD HH:MM:SS para DD/MM/YYYY HH:MM)
                    const dataHora = new Date(agendamento.data_agendamento);
                    const dataFormatada = dataHora.toLocaleDateString('pt-BR');
                    const horaFormatada = dataHora.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

                    // Determina a classe de cor pelo status
                    const statusClass = agendamento.status_consulta; // Ex: 'pendente', 'confirmada', 'cancelada', 'realizada'
                    const statusTexto = agendamento.status_consulta.charAt(0).toUpperCase() + agendamento.status_consulta.slice(1);
                    
                    const cardHtml = `
                        <div class="agendamento-card">
                            <div>
                                <span class="tag-status ${statusClass}">${statusTexto}</span>
                                <strong>Profissional:</strong> ${agendamento.nome_profissional}
                                <strong>Data:</strong> ${dataFormatada} às ${horaFormatada}
                            </div>
                            <a href="agendamento-detalhe.html?id=${agendamento.id_agendamento}" title="Ver Detalhes e Cancelar">Ver Detalhes</a>
                        </div>
                    `;
                    agendamentosLista.insertAdjacentHTML('beforeend', cardHtml);
                });

            } else {
                 agendamentosLista.innerHTML = `<p style="text-align: center; color: #7C8F88;">Nenhum agendamento ativo encontrado.</p>`;
            }

        } catch (error) {
            console.error('Erro ao buscar agendamentos:', error);
            agendamentosLista.innerHTML = `<p style="text-align: center; color: red;">Erro ao carregar seus agendamentos.</p>`;
        }
    }

    async function submeterAtualizacaoPerfil(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = document.getElementById('btn-atualizar-perfil');
        const statusMessage = document.getElementById('status-mensagem-perfil');
        const senhaInput = document.getElementById('senha_usuario');
        
        statusMessage.textContent = '';

        if (senhaInput.value && senhaInput.value.length < 6) {
             statusMessage.textContent = 'A nova senha deve ter pelo menos 6 caracteres.';
             statusMessage.style.color = 'red';
             return;
        }

        submitButton.textContent = 'Aguarde...';
        submitButton.disabled = true;

        try {
            const formData = new URLSearchParams(new FormData(form));

            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                statusMessage.textContent = result.message || 'Perfil atualizado!';
                statusMessage.style.color = 'green';
                if (result.userName) {
                    // Atualiza o nome da sessão para o header/menu lateral
                    sessionStorage.setItem('welcomeUserName', result.userName);
                    if (window.ativarMenuLateral) window.location.reload(); 
                }
                senhaInput.value = ''; // Limpa a senha
            } else {
                throw new Error(result.message || 'Erro ao atualizar. Tente novamente.');
            }

        } catch (error) {
            console.error('Erro ao atualizar perfil:', error);
            statusMessage.textContent = `Erro: ${error.message}.`;
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Atualizar Perfil';
            submitButton.disabled = false;
        }
    }
    
    carregarDadosDoPerfil();
});