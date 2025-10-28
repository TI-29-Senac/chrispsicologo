// js/minha-conta.js

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('minha-conta-container');
    const userName = sessionStorage.getItem('welcomeUserName');

    // Referências para o modal de avaliação
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

    // Se não estiver logado (nenhum nome de usuário na sessão do navegador)
    if (!userName) {
        container.innerHTML = `
            <div class="formulario-registro-container minha-conta-card">
                <h3>Acesso Restrito</h3>
                <p class="subtitulo-registro" style="color: #5D6D68; margin-bottom: 20px;">Você precisa fazer login para aceder a esta página.</p>
                <div style="text-align: center;">
                    <a href="#" onclick="abrirLoginModal(); return false;" class="botao-link" style="padding: 10px 20px; background-color: #5D6D68; color: white;">Fazer Login</a>
                </div>
            </div>
        `;
        return; // Interrompe a execução se não estiver logado
    }

    // --- Funções para o modal de avaliação ---
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
        if (modalAvaliacaoOverlay) {
            modalAvaliacaoOverlay.classList.remove('open');
        }
    };

    function gerarEstrelas(notaAtual = 0) {
        if (!estrelasContainer) return;
        estrelasContainer.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const span = document.createElement('span');
            span.classList.add('estrela-avaliacao');
            span.dataset.valor = i;
            span.innerHTML = '&#9733;';
            if (i <= notaAtual) {
                span.classList.add('selecionada');
            }
            estrelasContainer.appendChild(span);
        }
        adicionarListenersEstrelas();
    }

    function adicionarListenersEstrelas() {
         if (!estrelasContainer) return;
        const estrelas = estrelasContainer.querySelectorAll('.estrela-avaliacao');

        estrelas.forEach(estrela => {
            estrela.addEventListener('mouseover', function() {
                resetarCoresEstrelas();
                const valorAtual = parseInt(this.dataset.valor, 10);
                estrelas.forEach((s, index) => {
                    if (index < valorAtual) {
                        s.style.color = '#ffc107';
                    }
                });
            });

            estrela.addEventListener('mouseout', function() {
                resetarCoresEstrelas();
                const notaSelecionada = parseInt(notaInputHidden.value, 10);
                estrelas.forEach((s, index) => {
                     if (index < notaSelecionada) {
                         s.classList.add('selecionada');
                     }
                });
            });

            estrela.addEventListener('click', function() {
                const notaSelecionada = parseInt(this.dataset.valor, 10);
                notaInputHidden.value = notaSelecionada;
                estrelas.forEach((s, index) => {
                    if (index < notaSelecionada) {
                        s.classList.add('selecionada');
                    } else {
                        s.classList.remove('selecionada');
                    }
                });
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

    // --- Listener para submit do formulário de avaliação ---
    if (formAvaliacao) {
        formAvaliacao.addEventListener('submit', async (e) => {
            e.preventDefault();
            statusMsgAvaliacao.textContent = '';

            const nota = parseInt(notaInputHidden.value, 10);
            const descricao = descricaoInput.value.trim();
            const idProf = idProfissionalInputHidden.value;
            const idAgend = idAgendamentoInputHidden.value;

            if (nota === 0) {
                statusMsgAvaliacao.textContent = 'Por favor, selecione uma nota (1 a 5 estrelas).';
                statusMsgAvaliacao.style.color = 'red';
                return;
            }
             if (!descricao) {
                statusMsgAvaliacao.textContent = 'Por favor, deixe um comentário.';
                statusMsgAvaliacao.style.color = 'red';
                return;
            }

            btnEnviarAvaliacao.textContent = 'A enviar...';
            btnEnviarAvaliacao.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('id_profissional', idProf);
                formData.append('nota_avaliacao', nota);
                formData.append('descricao_avaliacao', descricao);

                const response = await fetch('/backend/api/cliente/avaliar', { // Endpoint da avaliação está correto
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    statusMsgAvaliacao.textContent = result.message;
                    statusMsgAvaliacao.style.color = 'green';

                    const btnAvaliarOriginal = document.querySelector(`.btn-avaliar[data-agendamento-id="${idAgend}"]`);
                    if (btnAvaliarOriginal) {
                         btnAvaliarOriginal.textContent = 'Avaliado';
                         btnAvaliarOriginal.disabled = true;
                         btnAvaliarOriginal.style.opacity = '0.6';
                         btnAvaliarOriginal.style.cursor = 'not-allowed';
                         btnAvaliarOriginal.onclick = null;
                    }
                    setTimeout(fecharModalAvaliacao, 2500);
                } else {
                    throw new Error(result.message || `Erro ${response.status}`);
                }

            } catch (error) {
                console.error('Erro ao enviar avaliação:', error);
                statusMsgAvaliacao.textContent = `Erro: ${error.message}`;
                statusMsgAvaliacao.style.color = 'red';
            } finally {
                if (statusMsgAvaliacao.style.color === 'red') {
                    btnEnviarAvaliacao.textContent = 'Enviar Avaliação';
                    btnEnviarAvaliacao.disabled = false;
                }
            }
        });
    }
    // --- Fim das funções e listener do modal ---


    // --- Função para carregar os dados do perfil via API ---
    async function carregarDadosDoPerfil() {
        console.log('Iniciando carregamento dos dados do perfil...'); // Log 1
        try {
            // *** CORREÇÃO APLICADA AQUI ***
            const response = await fetch('/backend/usuarios'); // Usa a rota GET /usuarios
            // *** FIM DA CORREÇÃO ***

            console.log('Resposta do fetch recebida:', response.status, response.statusText); // Log 2

            if (!response.ok) {
                 // Tenta ler o corpo da resposta mesmo em caso de erro, pode conter uma mensagem JSON
                 let errorMsg = `Erro ${response.status}: ${response.statusText}`;
                 try {
                     const errorData = await response.json();
                     errorMsg = errorData.message || errorMsg;
                 } catch (jsonError) {
                     // Ignora se não for JSON
                 }
                 throw new Error(errorMsg);
            }

            const result = await response.json();
            console.log('Dados recebidos da API:', result); // Log 3 (Dados JSON brutos)

            if (result.success && result.data) {
                renderizarInterface(result.data); // Passa apenas os dados do usuário
            } else {
                throw new Error(result.message || 'Resposta da API não indica sucesso ou falta dados.');
            }
        } catch (error) {
            console.error('Erro ao buscar ou processar dados do perfil:', error); // Log 4 (Erro final)
            container.innerHTML = `<p style="color: red; text-align: center;">Erro ao carregar informações da conta: ${error.message}</p>`;
        }
    }

    // --- Função para renderizar a interface com os dados do usuário ---
    function renderizarInterface(usuario) {
        console.log('Dados do usuário passados para renderizarInterface:', usuario); // Log 5
        if (!usuario || typeof usuario !== 'object' || !usuario.nome_usuario) { // Verificação mais robusta
             console.error('Objeto usuario inválido ou sem nome_usuario em renderizarInterface');
             container.innerHTML = '<p style="color: red; text-align: center;">Erro ao processar dados do perfil recebidos.</p>';
             return;
        }

        // Gera o HTML da página usando os dados do usuário
        container.innerHTML = `
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
        console.log('Interface renderizada no HTML.'); // Log 6

        // Adiciona listeners para a navegação e o formulário de perfil DEPOIS de inserir o HTML
        document.querySelectorAll('.minha-conta-nav .nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                const secao = this.dataset.secao;
                document.querySelectorAll('.minha-conta-nav .nav-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.secao-conteudo').forEach(s => s.classList.add('hidden'));
                this.classList.add('active');
                document.getElementById(`secao-${secao}`).classList.remove('hidden');
                if (secao === 'agendamentos') {
                    renderizarAgendamentos(); // Chama a função para carregar agendamentos
                }
            });
        });

        const formAtualizar = document.getElementById('form-atualizar-perfil');
        if (formAtualizar) {
             formAtualizar.addEventListener('submit', submeterAtualizacaoPerfil);
        } else {
             console.error('Formulário #form-atualizar-perfil não encontrado após renderizar.');
        }
    }

    // --- Função para buscar e renderizar os agendamentos ---
    async function renderizarAgendamentos() {
        const agendamentosLista = document.getElementById('agendamentos-lista');
        if (!agendamentosLista) return; // Verifica se o elemento existe

        agendamentosLista.innerHTML = '<p style="text-align: center; color: #5D6D68;">Buscando seus agendamentos...</p>';

        try {
            // *** Endpoint para buscar agendamentos do cliente ***
            const response = await fetch('/backend/api/cliente/meus-agendamentos');
            // *** Fim do Endpoint ***

            if (!response.ok) {
                 let errorMsg = `Erro ${response.status}: ${response.statusText}`;
                 try {
                     const errorData = await response.json();
                     errorMsg = errorData.message || errorMsg;
                 } catch (jsonError) { /* Ignora */ }
                 throw new Error(errorMsg);
            }

            const result = await response.json();

            if (result.success && Array.isArray(result.agendamentos)) { // Verifica se é array
                if (result.agendamentos.length > 0) {
                    agendamentosLista.innerHTML = '<h4>Seus Agendamentos:</h4>';

                    result.agendamentos.forEach(agendamento => {
                        // Validação básica dos dados do agendamento
                        if (!agendamento || !agendamento.data_agendamento || !agendamento.status_consulta || !agendamento.nome_profissional) {
                             console.warn('Agendamento com dados inválidos:', agendamento);
                             return; // Pula este agendamento
                        }

                        let dataHora;
                        try {
                            dataHora = new Date(agendamento.data_agendamento);
                             // Verifica se a data é válida
                             if (isNaN(dataHora.getTime())) throw new Error('Data inválida');
                        } catch(e) {
                             console.error('Erro ao processar data do agendamento:', agendamento.data_agendamento, e);
                             return; // Pula agendamento com data inválida
                        }

                        const dataFormatada = dataHora.toLocaleDateString('pt-BR', { timeZone: 'UTC' }); // Adiciona UTC para evitar problemas de fuso
                        const horaFormatada = dataHora.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                        const statusClass = agendamento.status_consulta.toLowerCase(); // Garante minúsculas
                        const statusTexto = agendamento.status_consulta.charAt(0).toUpperCase() + agendamento.status_consulta.slice(1);

                        let botaoAcaoHTML = '';
                        if (statusClass === 'realizada') {
                            // Verifica se id_profissional existe antes de criar o botão
                            if (agendamento.id_profissional && agendamento.id_agendamento) {
                                botaoAcaoHTML = `
                                    <button class="btn-avaliar"
                                            style="margin-left: 15px; padding: 5px 10px; font-size: 0.9rem; cursor: pointer;"
                                            data-agendamento-id="${agendamento.id_agendamento}"
                                            data-profissional-id="${agendamento.id_profissional}"
                                            data-profissional-nome="${agendamento.nome_profissional}"
                                            onclick="abrirModalAvaliacao('${agendamento.id_agendamento}', '${agendamento.id_profissional}', '${agendamento.nome_profissional}')">
                                        Avaliar
                                    </button>`;
                            } else {
                                console.warn('IDs faltando para o botão de avaliar:', agendamento);
                            }
                        }

                        const cardHtml = `
                            <div class="agendamento-card">
                                <div>
                                    <span class="tag-status ${statusClass}">${statusTexto}</span>
                                    <br><strong>Profissional:</strong> ${agendamento.nome_profissional} <br>
                                    <strong>Data:</strong> ${dataFormatada} às ${horaFormatada}
                                </div>
                                <div>
                                    ${botaoAcaoHTML}
                                </div>
                            </div>
                        `;
                        agendamentosLista.insertAdjacentHTML('beforeend', cardHtml);
                    });
                } else {
                    agendamentosLista.innerHTML = `<p style="text-align: center; color: #7C8F88;">Nenhum agendamento encontrado.</p>`;
                }
            } else {
                 throw new Error(result.message || 'Resposta da API de agendamentos inválida.');
            }

        } catch (error) {
            console.error('Erro ao buscar agendamentos:', error);
            agendamentosLista.innerHTML = `<p style="text-align: center; color: red;">Erro ao carregar seus agendamentos: ${error.message}</p>`;
        }
    }


    // --- Função para submeter a atualização do perfil ---
    async function submeterAtualizacaoPerfil(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = document.getElementById('btn-atualizar-perfil');
        const statusMessage = document.getElementById('status-mensagem-perfil');

        statusMessage.textContent = 'A atualizar...';
        statusMessage.style.color = '#5D6D68';
        submitButton.disabled = true;

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, { // Endpoint: /backend/api/cliente/atualizar-perfil
                method: 'POST',
                body: new URLSearchParams(formData)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                statusMessage.textContent = result.message || 'Perfil atualizado com sucesso!';
                statusMessage.style.color = 'green';
                 // Atualiza o nome na sessão do navegador, se foi alterado
                if (result.userName && sessionStorage.getItem('welcomeUserName') !== result.userName) {
                     sessionStorage.setItem('welcomeUserName', result.userName);
                     // Atualiza o título H3 na página (opcional, pode recarregar)
                     const tituloConta = container.querySelector('h3');
                     if (tituloConta) tituloConta.textContent = `Minha Conta - ${result.userName.split(' ')[0]}`;
                }
                 // Limpa o campo de nova senha por segurança
                 const senhaInput = document.getElementById('senha_usuario');
                 if(senhaInput) senhaInput.value = '';

            } else {
                throw new Error(result.message || `Erro ${response.status}`);
            }
        } catch (error) {
            console.error('Erro ao atualizar perfil:', error);
            statusMessage.textContent = `Erro: ${error.message}. Tente novamente.`;
            statusMessage.style.color = 'red';
        } finally {
            submitButton.textContent = 'Atualizar Perfil';
            submitButton.disabled = false;
        }
    }


    carregarDadosDoPerfil();

}); 