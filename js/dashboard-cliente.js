// js/dashboard-cliente.js - Sidebar Layout

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('minha-conta-container');
    const userName = sessionStorage.getItem('welcomeUserName');

    // --- Modal References (Avaliação) ---
    const modalAvaliacaoOverlay = document.getElementById('modal-avaliacao-overlay');
    const modalAvaliacaoTitulo = document.getElementById('modal-avaliacao-titulo');
    const modalAvaliacaoSubtitulo = document.getElementById('modal-avaliacao-subtitulo');
    const formAvaliacao = document.getElementById('form-avaliacao-cliente');
    const estrelasContainer = document.querySelector('.estrelas-input');
    const notaInputHidden = document.getElementById('avaliacao-nota');
    const idProfissionalInputHidden = document.getElementById('avaliacao-id-profissional');
    const idAgendamentoInputHidden = document.getElementById('avaliacao-id-agendamento');
    const descricaoInput = document.getElementById('avaliacao-descricao');
    const btnEnviarAvaliacao = document.getElementById('btn-enviar-avaliacao');
    const statusMsgAvaliacao = document.getElementById('status-mensagem-avaliacao');

    // --- Auth Check ---
    if (!userName) {
        container.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; min-height: 400px; width: 100%;">
                <div class="card-box" style="text-align: center; max-width: 450px; padding: 40px; background-color: #fff; border: 1px solid #ffcccc;">
                    <i class="ph ph-lock-key" style="font-size: 3rem; color: #d32f2f; margin-bottom: 20px;"></i>
                    <h3 style="color: #d32f2f; margin-bottom: 15px;">Acesso Restrito</h3>
                    <p style="color: #555; margin-bottom: 25px; font-size: 1.1rem;">Você precisa fazer login para acessar esta página.</p>
                    <button onclick="abrirLoginModal()" class="btn-primary" style="margin: 0 auto; min-width: 200px; justify-content: center; background-color: #5D6D68; color: #ffffff;">
                        Fazer Login
                    </button>
                    <p style="margin-top: 15px; font-size: 0.9rem;">
                        <a href="/index.html" style="color: #666; text-decoration: underline;">Voltar para o Início</a>
                    </p>
                </div>
            </div>
        `;
        return;
    }

    // --- Main Render Function ---
    function renderizarInterface(usuario) {
        console.log('Rendering Dashboard for:', usuario.nome_usuario);

        const escapeHtml = (unsafe) => {
            if (!unsafe) return '';
            return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };

        const nomeSeguro = escapeHtml(usuario.nome_usuario);
        const emailSeguro = escapeHtml(usuario.email_usuario);
        const cpfSeguro = escapeHtml(usuario.cpf);

        // Initials
        const nomes = nomeSeguro.split(' ');
        const iniciais = (nomes[0][0] + (nomes.length > 1 ? nomes[nomes.length - 1][0] : '')).toUpperCase();
        const primeiroNome = nomes[0];

        container.innerHTML = `
            <div class="dashboard-wrapper">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="sidebar-header">
                        <div class="user-avatar-small">
                            <i class="ph ph-user"></i>
                        </div>
                        <div class="user-welcome">
                            <h3>Olá, ${primeiroNome} <i class="ph ph-hand-waving" style="color: #666;"></i></h3>
                        </div>
                    </div>

                    <nav class="sidebar-nav">
                        <button class="nav-item active" data-target="perfil">
                            <i class="ph ph-user"></i> Meu Perfil
                        </button>
                        <button class="nav-item" data-target="agendamentos">
                            <i class="ph ph-calendar-blank"></i> Meus Agendamentos
                        </button>
                        <button class="nav-item" data-target="financeiro">
                            <i class="ph ph-wallet"></i> Financeiro
                        </button>
                        <button class="nav-item" data-target="seguranca">
                            <i class="ph ph-lock-key"></i> Segurança
                        </button>
                        
                        <button class="nav-item logout" onclick="performLogout(event)">
                            <i class="ph ph-sign-out"></i> Sair
                        </button>
                    </nav>
                </aside>

                <!-- Content Area -->
                <main class="dashboard-content">
                    
                    <!-- Header Global do Content -->
                    <div class="content-header">
                        <h1 id="page-title">Meu Perfil</h1>
                        <p id="page-subtitle">Cuidar da mente é um processo. Obrigado por estar aqui.</p>
                    </div>

                    <!-- Seção Perfil -->
                    <div id="section-perfil" class="dashboard-section">
                        
                        <div class="card-box">
                            <div class="card-header-internal">
                                <i class="ph ph-user"></i> <h3>Dados Pessoais</h3>
                            </div>
                            
                            <form id="form-perfil-dados" action="/backend/api/cliente/atualizar-perfil" method="POST">
                                <div class="form-group">
                                    <label>Nome completo</label>
                                    <input type="text" name="nome_usuario" value="${nomeSeguro}">
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="email" name="email_usuario" value="${emailSeguro}">
                                </div>
                                <div class="form-group">
                                    <label>CPF (Opcional)</label>
                                    <input type="text" name="cpf" value="${cpfSeguro}">
                                </div>
                            </form>
                        </div>

                        <div class="card-box">
                            <div class="card-header-internal">
                                <i class="ph ph-lock"></i> <h3>Segurança</h3>
                            </div>
                             <div class="form-group">
                                <label>Alterar senha</label>
                                <input type="password" form="form-perfil-dados" name="senha_usuario" placeholder="Deixe em branco para manter senha atual">
                            </div>
                        </div>

                        <div class="btn-group-row">
                             <button class="btn-secondary" onclick="performLogout(event)">
                                <i class="ph ph-power"></i> Sair da conta
                            </button>
                            <button type="button" class="btn-primary" onclick="document.getElementById('form-perfil-dados').dispatchEvent(new Event('submit'))">
                                <i class="ph ph-check"></i> Salvar alterações
                            </button>
                        </div>
                         <p id="status-mensagem-perfil" style="margin-top: 15px; text-align: right; font-size: 0.9rem;"></p>

                    </div>

                    <!-- Seção Agendamentos -->
                    <div id="section-agendamentos" class="dashboard-section hidden">
                        <div id="agendamentos-lista">
                             <p style="color: #666; text-align: center;">Carregando...</p>
                        </div>
                    </div>

                    <!-- Seção Financeiro -->
                    <div id="section-financeiro" class="dashboard-section hidden">
                         <div id="financeiro-lista">
                             <p style="color: #666; text-align: center;">Carregando...</p>
                        </div>
                    </div>

                     <!-- Seção Segurança (Separada se o usuario clicar no menu) -->
                     <div id="section-seguranca" class="dashboard-section hidden">
                        <div class="card-box">
                            <div class="card-header-internal">
                                <i class="ph ph-shield-check"></i> <h3>Segurança e Privacidade</h3>
                            </div>
                            <p style="margin-bottom: 20px; color: #ffffffff;">Gerencie suas credenciais de acesso.</p>
                             <form id="form-seguranca-only" onsubmit="atualizarSenhaSeparado(event)">
                                <div class="form-group">
                                    <label>Nova Senha</label>
                                    <input type="password" name="senha_usuario" placeholder="Digite a nova senha">
                                </div>
                                <button type="submit" class="btn-primary">Atualizar Senha</button>
                             </form>
                        </div>
                     </div>

                </main>
            </div>
        `;

        setupNavigation();

        // Listener do Form Perfil
        const formPerfil = document.getElementById('form-perfil-dados');
        if (formPerfil) {
            formPerfil.addEventListener('submit', submeterAtualizacaoPerfil);
        }
    }

    function setupNavigation() {
        const buttons = document.querySelectorAll('.nav-item[data-target]');
        const sections = document.querySelectorAll('.dashboard-section');
        const title = document.getElementById('page-title');
        const subtitle = document.getElementById('page-subtitle');

        const titles = {
            'perfil': { t: 'Meu Perfil', s: 'Cuidar da mente é um processo. Obrigado por estar aqui.' },
            'agendamentos': { t: 'Meus Agendamentos', s: 'Consulte seu histórico e próximas sessões.' },
            'financeiro': { t: 'Financeiro', s: 'Acompanhe seus pagamentos e faturas.' },
            'seguranca': { t: 'Segurança', s: 'Mantenha sua conta protegida.' }
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Active Class
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Show Section
                const target = btn.dataset.target;
                sections.forEach(s => s.classList.add('hidden'));
                document.getElementById(`section-${target}`).classList.remove('hidden');

                // Update Header
                if (titles[target]) {
                    title.textContent = titles[target].t;
                    subtitle.textContent = titles[target].s;
                }

                // Load Data if needed
                if (target === 'agendamentos') renderizarAgendamentos();
                if (target === 'financeiro') renderizarFinanceiro();
            });
        });
    }

    // --- Logic for Updates (Reused/Adapted) ---
    async function submeterAtualizacaoPerfil(event) {
        event.preventDefault();
        const form = event.target;
        const statusMsg = document.getElementById('status-mensagem-perfil');

        statusMsg.textContent = 'Aguarde...';
        statusMsg.style.color = '#666';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: new URLSearchParams(formData)
            });
            const result = await response.json();
            if (response.ok && result.success) {
                statusMsg.textContent = 'Salvo com sucesso!';
                statusMsg.style.color = 'green';
                setTimeout(() => statusMsg.textContent = '', 3000);
            } else {
                throw new Error(result.message || 'Erro ao salvar');
            }
        } catch (e) {
            statusMsg.textContent = 'Erro: ' + e.message;
            statusMsg.style.color = 'red';
        }
    }

    // --- Modal Logic (Same as before) ---
    window.abrirModalAvaliacao = (idAgendamento, idProfissional, nomeProfissional) => {
        if (!modalAvaliacaoOverlay || !formAvaliacao) return;
        modalAvaliacaoTitulo.textContent = `Avaliar Atendimento`;
        modalAvaliacaoSubtitulo.textContent = `Como foi sua consulta com ${nomeProfissional}?`;
        idProfissionalInputHidden.value = idProfissional;
        idAgendamentoInputHidden.value = idAgendamento;
        notaInputHidden.value = "0";
        descricaoInput.value = "";
        statusMsgAvaliacao.textContent = "";
        btnEnviarAvaliacao.disabled = false;
        btnEnviarAvaliacao.textContent = "Enviar Avaliação";
        gerarEstrelas();
        modalAvaliacaoOverlay.classList.add('open');
    };

    window.fecharModalAvaliacao = () => {
        if (modalAvaliacaoOverlay) modalAvaliacaoOverlay.classList.remove('open');
    };

    function gerarEstrelas(notaAtual = 0) {
        if (!estrelasContainer) return;
        estrelasContainer.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const span = document.createElement('span');
            span.classList.add('estrela-avaliacao');
            span.dataset.valor = i;
            span.innerHTML = '&#9733;';
            if (i <= notaAtual) span.classList.add('selecionada');
            estrelasContainer.appendChild(span);
        }
        adicionarListenersEstrelas();
    }

    function adicionarListenersEstrelas() {
        if (!estrelasContainer) return;
        const estrelas = estrelasContainer.querySelectorAll('.estrela-avaliacao');
        estrelas.forEach(estrela => {
            estrela.addEventListener('mouseover', function () {
                resetarCoresEstrelas();
                const valorAtual = parseInt(this.dataset.valor, 10);
                estrelas.forEach((s, index) => { if (index < valorAtual) s.style.color = '#ffc107'; });
            });
            estrela.addEventListener('mouseout', function () {
                resetarCoresEstrelas();
                const notaSelecionada = parseInt(notaInputHidden.value, 10);
                estrelas.forEach((s, index) => { if (index < notaSelecionada) s.classList.add('selecionada'); });
            });
            estrela.addEventListener('click', function () {
                const notaSelecionada = parseInt(this.dataset.valor, 10);
                notaInputHidden.value = notaSelecionada;
                estrelas.forEach((s, index) => { if (index < notaSelecionada) s.classList.add('selecionada'); else s.classList.remove('selecionada'); });
            });
        });
    }

    function resetarCoresEstrelas() {
        if (!estrelasContainer) return;
        estrelasContainer.querySelectorAll('.estrela-avaliacao').forEach(s => {
            s.style.color = '';
            s.classList.remove('selecionada');
        });
    }

    // --- Data Loaders (Agendamentos / Financeiro) ---
    // (Adapted logic to output list items or table rows into the new containers)
    async function renderizarAgendamentos() {
        const list = document.getElementById('agendamentos-lista');
        if (!list) return;
        list.innerHTML = '<p>Carregando agendamentos...</p>';

        try {
            const res = await fetch('/backend/api/cliente/meus-agendamentos', { headers: getAuthHeaders() });
            const data = await res.json();

            if (data.success && data.agendamentos && data.agendamentos.length > 0) {
                list.innerHTML = '';
                data.agendamentos.forEach(ag => {
                    // Create clean card style for schedule
                    const date = new Date(ag.data_agendamento);
                    const card = document.createElement('div');
                    card.className = 'card-box'; // Reuse card style
                    card.style.padding = '15px';
                    card.style.marginBottom = '15px';
                    card.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: #ffffffff; font-size: 1.1rem;">${ag.nome_profissional}</strong>
                                <div style="color: #ffffffff; font-size: 0.9rem; margin-top: 5px;">
                                    <i class="ph ph-calendar"></i> ${date.toLocaleDateString('pt-BR')} às ${date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
                                </div>
                                </div>
                                <span class="status-badge ${getStatusClass(ag.status_consulta)}">
                                    ${ag.status_consulta}
                                </span>
                            </div>
                            <div>
                                ${getAvaliarButton(ag)}
                            </div>
                        </div>
                     `;
                    list.appendChild(card);
                });
            } else {
                list.innerHTML = '<p>Nenhum agendamento encontrado.</p>';
            }
        } catch (e) {
            console.error(e);
            list.innerHTML = '<p style="color: red">Erro ao carregar agendamentos.</p>';
        }
    }

    function getAvaliarButton(ag) {
        if (ag.status_consulta.toLowerCase() === 'realizada') {
            if (ag.ja_avaliado) {
                return `<button class="btn-secondary" disabled style="opacity: 0.6; font-size: 0.8rem;">Avaliado</button>`;
            } else {
                return `<button class="btn-primary" style="font-size: 0.8rem; padding: 8px 15px;" onclick="abrirModalAvaliacao('${ag.id_agendamento}', '${ag.id_profissional}', '${ag.nome_profissional}')">Avaliar</button>`;
            }
        }
        return '';
    }

    async function renderizarFinanceiro() {
        const list = document.getElementById('financeiro-lista');
        if (!list) return;
        list.innerHTML = '<p>Carregando financeiro...</p>';

        try {
            const res = await fetch('/backend/api/cliente/financeiro', { headers: getAuthHeaders() });
            const result = await res.json();
            const data = result.data || [];

            if (data.length > 0) {
                let table = `
                    <div class="card-box" style="padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #e8e8e8;">
                            <tr>
                                <th style="padding: 15px; text-align: left; color: #555;">Data</th>
                                <th style="padding: 15px; text-align: left; color: #555;">Valor</th>
                                <th style="padding: 15px; text-align: left; color: #555;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                 `;
                data.forEach(p => {
                    table += `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">${new Date(p.data_pagamento).toLocaleDateString('pt-BR')}</td>
                            <td style="padding: 15px;">R$ ${parseFloat(p.valor_consulta).toFixed(2)}</td>
                             <td style="padding: 15px;">${p.tipo_pagamento}</td>
                        </tr>
                     `;
                });
                table += '</tbody></table></div>';
                list.innerHTML = table;
            } else {
                list.innerHTML = '<p>Nenhum registro financeiro.</p>';
            }
        } catch (e) {
            list.innerHTML = '<p style="color: red">Erro ao carregar financeiro.</p>';
        }
    }

    // --- Helpers ---
    function getAuthHeaders() {
        return { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` };
    }

    function getStatusClass(status) {
        if (!status) return '';
        switch (status.toLowerCase()) {
            case 'realizada': return 'status-realizada';
            case 'confirmada': return 'status-confirmada';
            case 'pendente': return 'status-pendente';
            case 'cancelada': return 'status-cancelada';
            default: return '';
        }
    }

    async function carregarDadosDoPerfil() {
        try {
            const response = await fetch('/backend/api/cliente/meu-perfil', {
                headers: getAuthHeaders()
            });
            if (response.ok) {
                const data = await response.json();
                renderizarInterface(data.data || data);
            } else {
                container.innerHTML = '<p>Erro ao carregar perfil. Faça login novamente.</p>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Listener Avaliação
    if (formAvaliacao) {
        formAvaliacao.addEventListener('submit', async (e) => {
            e.preventDefault();
            // ... (Same logic as before for submitting review) ...
            const nota = parseInt(notaInputHidden.value, 10);
            const descricao = descricaoInput.value.trim();
            const idProf = idProfissionalInputHidden.value;

            if (nota === 0 || !descricao) {
                statusMsgAvaliacao.textContent = 'Preencha todos os campos.';
                return;
            }
            // Send...
            try {
                const formData = new URLSearchParams();
                formData.append('id_profissional', idProf);
                formData.append('nota_avaliacao', nota);
                formData.append('descricao_avaliacao', descricao);

                await fetch('/backend/api/cliente/avaliar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...getAuthHeaders() },
                    body: formData
                });

                statusMsgAvaliacao.textContent = 'Avaliação enviada!';
                statusMsgAvaliacao.style.color = 'green';
                setTimeout(fecharModalAvaliacao, 2000);
                renderizarAgendamentos(); // Refresh button state
            } catch (error) {
                console.error(error);
            }
        });
    }

    carregarDadosDoPerfil();
});