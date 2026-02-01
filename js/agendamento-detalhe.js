document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('agendamento-container');
    const loadingMessage = document.getElementById('loading-message');

    const params = new URLSearchParams(window.location.search);
    const profissionalId = params.get('id');

    let profissionalData = null;
    let dataSelecionada = null;
    let horarioSelecionado = null;

    if (!profissionalId) {
        loadingMessage.textContent = 'Erro: ID do Profissional não fornecido na URL.';
        loadingMessage.style.color = 'red';
        return;
    }

    // --- PROTEÇÃO DA PÁGINA: Verifica se o usuário está logado ---
    const token = localStorage.getItem('auth_token');
    if (!token) {
        // Opção 1: Alertar e redirecionar para home onde o modal de login pode ser aberto
        alert('Você precisa estar logado para realizar um agendamento.');

        // Remove parâmetros da URL para evitar loops ou estados inconsistentes
        // window.history.replaceState({}, document.title, window.location.pathname);

        // Oculta o conteúdo para não "piscar" informações
        container.style.display = 'none';

        // Redireciona para a home
        window.location.href = '/index.html?openLogin=true';

        // Nota: Se preferir abrir o modal diretamente na página, seria necessário
        // garantir que o login.js já carregou e usar 'abrirLoginModal()', mas
        // se o usuário não logar, ele veria a página. O bloqueio exige redirecionamento
        // ou um overlay persistente.
        return;
    }
    // --- FIM DA PROTEÇÃO ---


    function getHojeFormatado() {
        const hoje = new Date();
        const ano = hoje.getFullYear();
        const mes = String(hoje.getMonth() + 1).padStart(2, '0');
        const dia = String(hoje.getDate()).padStart(2, '0');
        return `${ano}-${mes}-${dia}`;
    }

    async function carregarDadosIniciais() {
        loadingMessage.textContent = 'Carregando informações do profissional...';
        container.innerHTML = '';

        try {
            const resProfissional = await fetch(`/backend/profissionais/detalhe/${profissionalId}`);

            if (!resProfissional.ok) {
                const errorData = await resProfissional.json().catch(() => ({ message: 'Profissional não encontrado ou indisponível.' }));
                throw new Error(errorData.message);
            }

            profissionalData = await resProfissional.json();


            renderizarLayoutBase(profissionalData);
            loadingMessage.style.display = 'none';
            carregarAvaliacoesProfissional(profissionalId);


            const hoje = getHojeFormatado();
            const dateInput = document.getElementById('data-consulta');
            if (dateInput) {
                dateInput.value = hoje;
                dataSelecionada = hoje;
                buscarHorariosDisponiveis();
            }


        } catch (error) {
            console.error('Erro ao carregar dados do profissional:', error);
            container.innerHTML = `<p id="loading-message" style="color: red; text-align: center;">${error.message || 'Não foi possível carregar as informações. Tente novamente.'}</p>`;
        }
    }

    function renderizarLayoutBase(prof) {
        const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
        const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`;
        const fotoFinal = prof.img_profissional ? `/${prof.img_profissional}` : fotoUrlPadrao;
        const hojeFormatado = getHojeFormatado();
        const maxEspecialidades = 1;

        // Processar a string de especialidades
        const especialidadesArray = prof.especialidade ?
            prof.especialidade.split(',').map(s => s.trim()).filter(s => s.length > 0) :
            ["Psicoterapia Individual", "Terapia de Casal", "Psicoterapia Infantil", "Orientação Profissional"];

        const especialidadesHtml = especialidadesArray.map(esp => `
             <label><input type="checkbox" name="especialidade[]" value="${esp}"> ${esp}</label>
        `).join('');

        // Lógica de Tipos de Atendimento (Mantida)
        const defaultTipos = [{ icone: "img/icons/adulto.svg", texto: "Adultos" }];
        let tiposDeAtendimento = null;
        if (prof.tipos_atendimento) {
            try {
                tiposDeAtendimento = JSON.parse(prof.tipos_atendimento);
            } catch (e) {
                console.error("Erro ao parsear tipos_atendimento:", e);
            }
        }
        if (!tiposDeAtendimento || !Array.isArray(tiposDeAtendimento) || tiposDeAtendimento.length === 0) {
            tiposDeAtendimento = defaultTipos;
        }
        const tiposAtendimentoHtml = tiposDeAtendimento.map(t =>
            `<li><img src="/${t.icone}" class="icon-prof" alt=""> ${t.texto}</li>`
        ).join("");

        const valorSinalFormatado = parseFloat(prof.sinal_consulta || 0).toFixed(2).replace('.', ',');

        // --- ATUALIZAÇÃO DO FORMULÁRIO COM MENSAGENS DE ERRO ---
        const conteudoHTML = `
            <h2 class="titulo-aba-prof">Seu agendamento com ${prof.nome_usuario.split(' ')[0]}</h2>

            <div class="agendamento-detalhe-grid">
                 <div class="profissional-info-coluna">
                    <h3>${prof.nome_usuario}</h3>
                    <img src="${fotoFinal}" alt="${prof.nome_usuario}" class="profissional-info-foto">
                    
                    <h4>Duração e Tipo de Atendimento</h4>
                    <ul class="tipos-lista">
                        <li><img src="img/icons/relogio.svg" class="icon-prof"> Sessão com duração de 50min</li>
                        ${tiposAtendimentoHtml} </ul>
                    <div class="valor-consulta-box">
                        Valor da Consulta: <strong>R$ ${parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ',')}</strong>
                    </div>
                </div>
                <div class="profissional-bio-coluna">
                    <h4>Um pouco sobre ${prof.nome_usuario.split(' ')[0]}</h4>
                    <div id="bioContainer" class="read-more-container">
                        <p style="white-space: pre-wrap;">${prof.sobre || 'Biografia não disponível.'}</p>
                    </div>
                    <button id="bioReadMoreBtn" class="read-more-btn" style="display: none;">Ler Mais</button>
                </div>
            </div>

            <div class="agenda-container">
                <h3>Escolha a data e o horário</h3>
                <div class="seletor-data">
                    <label for="data-consulta">Data:</label>
                    <input type="date" id="data-consulta" name="data-consulta" min="${hojeFormatado}" value="${hojeFormatado}">
                </div>
                <div id="agenda-horarios-container">
                    <p id="agenda-feedback">Buscando horários para hoje...</p> 
                    <div class="agenda-body" style="display: none;"></div>
                </div>
            </div>

            <div class="confirmacao-container" id="confirmacao-container" style="display: none;">
                <h3>Confirmação</h3>
                <p id="horario-confirmacao-texto" style="text-align: center; margin-bottom: 20px; font-weight: bold;"></p>
                
                <form id="form-confirmacao" class="confirmacao-form">
                    <input type="hidden" id="id_profissional_hidden" name="id_profissional" value="${profissionalId}">
                    <input type="hidden" id="data_selecionada_hidden" name="data_selecionada">
                    <input type="hidden" id="horario_selecionado_hidden" name="horario_selecionado">

                    <div class="form-grupo">
                        <label for="nome-completo">Nome completo:</label>
                        <input type="text" id="nome-completo" name="nome-completo" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="exemplo@email.com" required>
                        <small id="erro-email" style="color: #ff6b6b; display: none; margin-top: 5px; font-weight: bold;">Formato de e-mail inválido.</small>
                    </div>

                    <div class="form-grupo-duplo">
                        <div class="form-grupo">
                            <label for="telefone">Telefone (com DDD):</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                            <small id="erro-telefone" style="color: #ff6b6b; display: none; margin-top: 5px; font-weight: bold;">Telefone incompleto.</small>
                        </div>
                        <div class="form-grupo">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                            <small id="erro-cpf" style="color: #ff6b6b; display: none; margin-top: 5px; font-weight: bold;">CPF incompleto.</small>
                        </div>
                    </div>
                    
                     <div class="form-grupo-duplo" style="margin-top: 10px;">
                        <div class="form-grupo pagamento-radio-group">
                            <label style="margin-bottom: 10px;">Forma de Pagamento:</label>
                            <div class="radio-option">
                                <input type="radio" id="pag_pix" name="forma_pagamento" value="pix" required checked>
                                <label for="pag_pix" style="display: inline; font-weight: normal;">Pix (Rápido e Confirmação Imediata)</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="pag_cartao" name="forma_pagamento" value="cartao">
                                <label for="pag_cartao" style="display: inline; font-weight: normal;">Cartão de Crédito/Débito</label>
                            </div>
                        </div>
                        <div class="form-grupo especialidades">     
                            <div class="lista-especialidades scrolling-checkbox-list">
                                ${especialidadesHtml} 
                            </div>
                        </div>
                    </div>

                    <div class="form-grupo-duplo" style="margin-top: 10px;">
                        <div class="form-grupo">
                            <button type="button" class="termos-link" id="openTerms">Ler Termos de pagamento</button>
                            <div class="checkbox-container">
                                <input type="checkbox" id="termos-aceitos" name="termos_aceitos" required style="position: absolute; height: 1px; width: 1px; overflow: hidden; clip: rect(1px, 1px, 1px, 1px);">
                                <label for="termos-aceitos" id="label-termos-aceitos" style="font-family: questrial, sans-serif; color: white; margin-top: 5px; cursor: pointer;">*É necessário aceitar os termos de pagamento.</label>
                            </div>
                        </div>
                        <div class="form-grupo" style="display: flex; align-items: center; justify-content: center; flex-direction: column; height: 100%;">
                             <p style="text-align: center; font-size: 1em; margin: 0; margin-top: -10px; padding: 20px; border: 1px solid #faf6ee; background-color: #faf6ee; color: #5D6D68; border-radius: 6px;">
                                O pagamento do sinal é necessário para confirmar a consulta. Você será redirecionado para o método escolhido.
                             </p>
                        </div>
                    </div>
                    <input type="hidden" id="tipo_pagamento_backend" name="tipo_pagamento" value="pix"> 
                    
                     <div class="form-rodape">
                        <div class="valor-sinal">
                            Valor do sinal: <strong>R$ ${valorSinalFormatado}</strong>
                         </div>
                      
                        <button type="submit" class="btn-pagamento" id="btn-confirmar-agendamento">Solicitar Agendamento e Pagar</button>
                    </div>
                     <p id="agendamento-status-message" style="margin-top: 15px; text-align: center; font-weight: bold;"></p>
                </form>
            </div>
        `;

        container.innerHTML = conteudoHTML;

        const inputCPF = document.getElementById('cpf');
        if (inputCPF) {
            inputCPF.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, ""); // Remove não dígitos
                if (value.length > 11) value = value.slice(0, 11); // Limita a 11 números

                // Aplica a máscara
                if (value.length > 9) {
                    value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, "$1.$2.$3-$4");
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{3})(\d{3})(\d{3}).*/, "$1.$2.$3");
                } else if (value.length > 3) {
                    value = value.replace(/^(\d{3})(\d{3}).*/, "$1.$2");
                }

                e.target.value = value;

                // Validação visual (mínimo 14 caracteres com a máscara)
                const erroCpf = document.getElementById('erro-cpf');
                if (value.length < 14) {
                    erroCpf.style.display = 'block';
                    e.target.style.border = '2px solid #ff6b6b';
                } else {
                    erroCpf.style.display = 'none';
                    e.target.style.border = '2px solid #99b198'; // Borda verde suave
                }
            });
        }

        // 2. Máscara e Validação de Telefone ( (11) 99999-9999 )
        const inputTelefone = document.getElementById('telefone');
        if (inputTelefone) {
            inputTelefone.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, "");
                if (value.length > 11) value = value.slice(0, 11);

                // Máscara
                if (value.length > 10) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
                } else if (value.length > 2) {
                    value = value.replace(/^(\d{2})(\d{0,5}).*/, "($1) $2");
                } else {
                    value = value.replace(/^(\d*)/, "($1");
                }

                e.target.value = value;

                // Validação visual (Mínimo 14 chars: (11) 9999-9999)
                const erroTel = document.getElementById('erro-telefone');
                if (value.length < 14) {
                    erroTel.style.display = 'block';
                    e.target.style.border = '2px solid #ff6b6b';
                } else {
                    erroTel.style.display = 'none';
                    e.target.style.border = '2px solid #99b198';
                }
            });
        }

        // 3. Validação de Email (Regex simples)
        const inputEmail = document.getElementById('email');
        if (inputEmail) {
            inputEmail.addEventListener('blur', function (e) { // Valida ao sair do campo
                const value = e.target.value;
                const erroEmail = document.getElementById('erro-email');
                // Regex padrão para xxx@xxx.xx
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (value && !emailPattern.test(value)) {
                    erroEmail.style.display = 'block';
                    e.target.style.border = '2px solid #ff6b6b';
                } else {
                    erroEmail.style.display = 'none';
                    if (value) e.target.style.border = '2px solid #99b198';
                    else e.target.style.border = '1px solid #ccc'; // Volta ao normal se vazio
                }
            });
        }

        // --- FIM DA LÓGICA DE VALIDAÇÃO ---

        // (O restante do código da função renderizarLayoutBase continua igual:
        // Lógica do botão "Ler Mais" da bio, checkboxes, etc...)

        const bioContainer = document.getElementById('bioContainer');
        const bioBtn = document.getElementById('bioReadMoreBtn');
        const bioText = bioContainer.querySelector('p');
        const alturaContraida = 450;

        if (bioText.scrollHeight > alturaContraida) {
            bioBtn.style.display = 'block';
            bioBtn.addEventListener('click', () => {
                if (bioContainer.classList.toggle('expanded')) {
                    bioBtn.textContent = 'Ler Menos';
                } else {
                    bioBtn.textContent = 'Ler Mais';
                }
            });
        }

        const checkboxesEspecialidade = document.querySelectorAll('.lista-especialidades input[name="especialidade[]"]');

        function atualizarLimiteCheckboxes() {
            const checkedCount = document.querySelectorAll('.lista-especialidades input[name="especialidade[]"]:checked').length;
            const atingiuLimite = checkedCount >= maxEspecialidades;
            checkboxesEspecialidade.forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.disabled = atingiuLimite;
                    checkbox.parentElement.classList.toggle('disabled-label', atingiuLimite);
                }
            });
        }

        checkboxesEspecialidade.forEach(checkbox => {
            checkbox.addEventListener('change', atualizarLimiteCheckboxes);
        });
        atualizarLimiteCheckboxes();

        const dateInput = document.getElementById('data-consulta');
        if (dateInput) {
            dateInput.addEventListener('change', buscarHorariosDisponiveis);
            // Melhora a UX permitindo clicar em qualquer lugar do input para abrir o calendário
            dateInput.addEventListener('click', function () {
                try {
                    if (typeof this.showPicker === 'function') {
                        this.showPicker();
                    }
                } catch (error) {
                    console.warn('Browser does not support showPicker() or prevented it:', error);
                }
            });
        }

        document.querySelectorAll('input[name="forma_pagamento"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('tipo_pagamento_backend').value = this.value;
            });
        });

        // Modificação no Listener do Submit para bloquear envio se houver erro
        const formConfirmacao = document.getElementById('form-confirmacao');
        if (formConfirmacao) {
            formConfirmacao.addEventListener('submit', function (e) {
                // Verifica se há mensagens de erro visíveis
                const erroCpf = document.getElementById('erro-cpf').style.display;
                const erroTel = document.getElementById('erro-telefone').style.display;
                const erroEmail = document.getElementById('erro-email').style.display;

                if (erroCpf === 'block' || erroTel === 'block' || erroEmail === 'block') {
                    e.preventDefault();
                    const msg = document.getElementById('agendamento-status-message');
                    msg.textContent = "Por favor, corrija os campos em vermelho antes de continuar.";
                    msg.style.color = "red";
                    return;
                }
                submeterAgendamento(e);
            });
        }

        // ... (Lógica do Modal de Termos - pode copiar do seu código original ou manter se já estiver lá) ...
        // Certifique-se de que a lógica do modal de termos (openTerms, termsOverlay, etc) está aqui embaixo também.
        setupTermosModal(); // Função auxiliar para organizar (veja abaixo)
    }

    // Função auxiliar para manter a lógica do modal de termos organizada
    function setupTermosModal() {
        const formTermsCheckbox = document.getElementById('termos-aceitos');
        const formTermsLabel = document.getElementById('label-termos-aceitos');
        const openBtn = document.getElementById('openTerms');
        const overlay = document.getElementById('termsOverlay');
        const modalCheckbox = document.getElementById('acceptCheckbox');
        const modalConfirmBtn = document.getElementById('confirmBtn');
        const modalCancelBtn = document.getElementById('cancelBtn');

        if (openBtn && overlay && modalCheckbox && modalConfirmBtn && modalCancelBtn && formTermsCheckbox && formTermsLabel) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                overlay.style.display = 'flex';
                modalCheckbox.checked = formTermsCheckbox.checked;
                modalConfirmBtn.disabled = !formTermsCheckbox.checked;
                modalCheckbox.focus();
            });

            modalCancelBtn.addEventListener('click', () => {
                overlay.style.display = 'none';
                modalCheckbox.checked = formTermsCheckbox.checked;
                modalConfirmBtn.disabled = !formTermsCheckbox.checked;
                openBtn.focus();
            });

            modalCheckbox.addEventListener('change', () => {
                modalConfirmBtn.disabled = !modalCheckbox.checked;
            });

            modalConfirmBtn.addEventListener('click', () => {
                if (!modalCheckbox.checked) return;
                formTermsCheckbox.checked = true;
                formTermsLabel.textContent = 'Termos de pagamento aceitos.';
                formTermsLabel.style.color = 'white';
                overlay.style.display = 'none';
                openBtn.focus();
                window.dispatchEvent(new CustomEvent('terms:accepted'));
            });

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && overlay.style.display === 'flex') {
                    modalCancelBtn.click();
                }
            });

            formTermsLabel.addEventListener('click', () => {
                openBtn.click();
            });
        }
    }



    async function buscarHorariosDisponiveis() {
        const dateInput = document.getElementById('data-consulta');
        dataSelecionada = dateInput.value;
        const horariosContainer = document.getElementById('agenda-horarios-container');
        const agendaBody = horariosContainer.querySelector('.agenda-body');
        const feedbackEl = document.getElementById('agenda-feedback');
        const confirmacaoContainer = document.getElementById('confirmacao-container');


        agendaBody.innerHTML = '';
        agendaBody.style.display = 'none';
        feedbackEl.textContent = 'Buscando horários...';
        feedbackEl.style.color = '#faf6ee';
        horarioSelecionado = null;
        confirmacaoContainer.style.display = 'none';

        if (!dataSelecionada) {
            feedbackEl.textContent = 'Selecione uma data válida.';
            return;
        }


        const hoje = getHojeFormatado();
        if (dataSelecionada < hoje) {
            feedbackEl.textContent = 'Não é possível agendar para datas passadas.';
            feedbackEl.style.color = '#ffdddd';
            return;
        }


        try {
            const response = await fetch(`/backend/agendamentos/disponibilidade/${profissionalId}/${dataSelecionada}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Não foi possível buscar os horários.');
            }

            if (result.horarios && result.horarios.length > 0) {
                const dataFormatada = new Date(dataSelecionada + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' });
                feedbackEl.textContent = `Horários disponíveis para ${dataFormatada}:`;
                renderizarBotoesHorario(result.horarios, agendaBody);
                agendaBody.style.display = 'grid';
            } else {
                feedbackEl.textContent = 'Nenhum horário disponível para esta data.';
            }

        } catch (error) {
            console.error('Erro ao buscar horários:', error);
            feedbackEl.textContent = error.message || 'Erro ao carregar horários.';
            feedbackEl.style.color = '#ffdddd';
        }
    }


    function renderizarBotoesHorario(horarios, container) {
        container.innerHTML = '';

        const horariosManha = horarios.filter(h => parseInt(h.split(':')[0]) < 13);
        const horariosTarde = horarios.filter(h => parseInt(h.split(':')[0]) >= 13);

        let htmlBotoes = '<div class="agenda-coluna">';
        if (horariosManha.length > 0) {
            htmlBotoes += horariosManha.map(hora => `<button class="horario-btn" data-horario="${hora}">${hora}</button>`).join('');
        } else {
            htmlBotoes += '<p style="font-size: 0.9em; text-align: center;">Sem horários<br>na manhã</p>';
        }
        htmlBotoes += '</div><div class="agenda-coluna">';
        if (horariosTarde.length > 0) {
            htmlBotoes += horariosTarde.map(hora => `<button class="horario-btn" data-horario="${hora}">${hora}</button>`).join('');
        } else {
            htmlBotoes += '<p style="font-size: 0.9em; text-align: center;">Sem horários<br>à tarde</p>';
        }
        htmlBotoes += '</div>';

        container.innerHTML = htmlBotoes;



        container.querySelectorAll('.horario-btn').forEach(btn => {
            btn.addEventListener('click', () => selecionarHorario(btn));
        });
    }


    function selecionarHorario(botaoClicado) {
        const container = botaoClicado.closest('.agenda-body');

        container.querySelectorAll('.horario-btn').forEach(btn => btn.classList.remove('selecionado'));


        botaoClicado.classList.add('selecionado');
        horarioSelecionado = botaoClicado.dataset.horario;


        const confirmacaoContainer = document.getElementById('confirmacao-container');
        const textoConfirmacao = document.getElementById('horario-confirmacao-texto');
        const dataInputHidden = document.getElementById('data_selecionada_hidden');
        const horarioInputHidden = document.getElementById('horario_selecionado_hidden');


        if (dataSelecionada && horarioSelecionado) {
            const dataFormatada = new Date(dataSelecionada + 'T00:00:00').toLocaleDateString('pt-BR', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' });
            textoConfirmacao.textContent = `Agendar para: ${dataFormatada} às ${horarioSelecionado}`;
            dataInputHidden.value = dataSelecionada;
            horarioInputHidden.value = horarioSelecionado;
            confirmacaoContainer.style.display = 'block';
        } else {
            confirmacaoContainer.style.display = 'none';
        }
    }


    async function submeterAgendamento(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = document.getElementById('btn-confirmar-agendamento');
        const statusMessage = document.getElementById('agendamento-status-message');

        const selectedPayment = document.querySelector('input[name="forma_pagamento"]:checked').value;

        statusMessage.textContent = 'Solicitando agendamento e pagamento...';
        statusMessage.style.color = '#faf6ee';
        submitButton.disabled = true;

        // Validação da checkbox de termos (ponte)
        const termosAceitos = document.getElementById('termos-aceitos');
        const labelAceitos = document.getElementById('label-termos-aceitos');
        if (!termosAceitos.checked) {
            statusMessage.textContent = 'Por favor, aceite os termos de pagamento para continuar.';
            statusMessage.style.color = 'red';
            labelAceitos.style.color = 'red'; // Garante que o label fique vermelho
            submitButton.disabled = false;

            // Tenta abrir o modal para o usuário
            const openBtn = document.getElementById('openTerms');
            if (openBtn) openBtn.click();

            return;
        }

        // Validação Explícita para Mobile
        if (!formData.get('data_selecionada') || !formData.get('horario_selecionado')) {
            statusMessage.textContent = 'Por favor, selecione uma data e um horário.';
            statusMessage.style.color = 'red';
            submitButton.disabled = false;
            submitButton.textContent = 'Confirmar Agendamento';
            return;
        }

        if (!selectedPayment) {
            statusMessage.textContent = 'Por favor, selecione uma forma de pagamento.';
            statusMessage.style.color = 'red';
            submitButton.disabled = false;
            submitButton.textContent = 'Confirmar Agendamento';
            return;
        }

        const formDataToSend = new FormData();
        // Copia os dados do form original e adiciona o tipo de pagamento
        for (var pair of formData.entries()) {
            formDataToSend.append(pair[0], pair[1]);
        }
        formDataToSend.append('tipo_pagamento', selectedPayment === 'cartao' ? 'credito' : selectedPayment);

        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch('/backend/agendamentos/salvar', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: new URLSearchParams(formDataToSend)
            });

            const result = await response.json();

            if (response.ok && result.success) {

                const agendamentoId = result.agendamentoId;
                let redirectUrl = '';

                switch (selectedPayment) {
                    case 'pix':
                        redirectUrl = `/pagamento-pix.html?id=${agendamentoId}`;
                        break;
                    case 'cartao':
                        redirectUrl = `/pagamento-cartao.html?id=${agendamentoId}`;
                        break;
                    default:
                        redirectUrl = `/index.html`;
                }

                statusMessage.textContent = result.message || 'Agendamento solicitado! Redirecionando para o pagamento...';
                statusMessage.style.color = 'lightgreen';

                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 2000);

            } else {
                throw new Error(result.message || `Erro ${response.status}`);
            }

        } catch (error) {
            console.error('Erro ao submeter agendamento:', error);
            statusMessage.textContent = `Erro: ${error.message}. Tente novamente.`;
            statusMessage.style.color = 'red';

            if (error.message.includes('horário acabou de ser reservado')) {
                buscarHorariosDisponiveis();
            }
        } finally {
            if (statusMessage.style.color === 'red') {
                submitButton.disabled = false;
            }
        }
    }



    function carregarAvaliacoesProfissional(id) {
        // Implementação omitida, mas a função é mantida no escopo original
    }


    carregarDadosIniciais();

});

// Função dummy para carregar avaliações (para evitar erros se não estiver definida)
async function carregarAvaliacoesProfissional(idProf) {
    const containerAvaliacoes = document.getElementById(`avaliacoes-detalhe-${idProf}`);
    if (!containerAvaliacoes) {
        // console.log("Container de avaliações não encontrado (normal se não houver).");
        return;
    }

    const notaMediaEl = containerAvaliacoes.querySelector('.nota-media-detalhe');
    const estrelasEl = containerAvaliacoes.querySelector('.estrelas-media-detalhe');
    const totalEl = containerAvaliacoes.querySelector('.total-avaliacoes-detalhe');

    if (!notaMediaEl || !estrelasEl || !totalEl) {
        console.error("Elementos de avaliação não encontrados no container.");
        return;
    }


    notaMediaEl.textContent = 'Carregando...';
    estrelasEl.innerHTML = "☆☆☆☆☆";
    totalEl.textContent = '';

    try {
        const response = await fetch(`/backend/avaliacoes?id=${idProf}`);
        if (!response.ok) throw new Error('Falha ao buscar avaliações');

        const comentarios = await response.json();

        if (!comentarios || comentarios.length === 0) {
            notaMediaEl.textContent = 'Sem avaliações';
            totalEl.textContent = '(0 avaliações)';
            return;
        }

        const total = comentarios.length;
        const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota_avaliacao), 0);
        const media = soma_notas / total;
        const media_arredondada = Math.round(media);

        notaMediaEl.textContent = `${media.toFixed(1)} de 5`;
        estrelasEl.innerHTML = `${"★".repeat(media_arredondada)}${"☆".repeat(5 - media_arredondada)}`;
        totalEl.textContent = `(${total} ${total === 1 ? 'avaliação' : 'avaliações'})`;

    } catch (error) {
        console.error('Erro ao carregar avaliações:', error);
        notaMediaEl.textContent = 'Erro ao carregar';
        totalEl.textContent = '';
    }
}