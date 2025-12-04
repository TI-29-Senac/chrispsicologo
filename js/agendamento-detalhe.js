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
            if(dateInput) {
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
        // Processar a string de especialidades do profissional do BD
        const especialidadesArray = prof.especialidade ? 
            prof.especialidade.split(',').map(s => s.trim()).filter(s => s.length > 0) : 
            ["Psicoterapia Individual", "Terapia de Casal", "Psicoterapia Infantil", "Orientação Profissional"]; // Fallback 
        
        // Gerar o HTML dos checkboxes de especialidade dinamicamente
        const especialidadesHtml = especialidadesArray.map(esp => `
             <label><input type="checkbox" name="especialidade[]" value="${esp}"> ${esp}</label>
        `).join('');

        
        // --- (INÍCIO) CORREÇÃO APLICADA AQUI ---
        const defaultTipos = [
            { icone: "img/icons/adulto.svg", texto: "Adultos" }
        ];
        
        let tiposDeAtendimento = null;
        if (prof.tipos_atendimento) { // Verifica se não é nulo
            try {
                // Converte a string JSON (ex: "[{...}]") em um array de objetos
                tiposDeAtendimento = JSON.parse(prof.tipos_atendimento);
            } catch (e) {
                console.error("Erro ao parsear tipos_atendimento:", e);
            }
        }
        // --- (FIM) CORREÇÃO APLICADA AQUI ---

        // Se falhou ou estava vazio, usa o fallback
        if (!tiposDeAtendimento || !Array.isArray(tiposDeAtendimento) || tiposDeAtendimento.length === 0) {
            tiposDeAtendimento = defaultTipos;
        }

        // Mapeia os tipos de atendimento para HTML
        const tiposAtendimentoHtml = tiposDeAtendimento.map(t => 
            `<li><img src="/${t.icone}" class="icon-prof" alt=""> ${t.texto}</li>`
        ).join("");


        const valorSinalFormatado = parseFloat(prof.sinal_consulta || 0).toFixed(2).replace('.', ',');

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
                    <div class="agenda-body" style="display: none;">

                    </div>
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
                        <input type="email" id="email" pattern="^[^@]+@[^@]+\.[a-zA-Z]{2,}$" required>

                    </div>
                    <div class="form-grupo-duplo">
                        <div class="form-grupo">
                            <label for="telefone">Telefone (com DDD):</label>
                            <input type="tel" id="telefone" name="telefone" required>
                        </div>
                        <div class="form-grupo">
                            <label for="cpf">CPF:</label>
                            <input id="cpf" type="text" pattern="^\d{3}\.\d{3}\.\d{3}-\d{2}$" required>
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
                                ${especialidadesHtml} </div>
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

        const bioContainer = document.getElementById('bioContainer');
        const bioBtn = document.getElementById('bioReadMoreBtn');
        const bioText = bioContainer.querySelector('p');
        
        // 1. Define a altura padrão (deve ser a mesma do CSS: 150px)
        const alturaContraida = 450; 

        // 2. Verifica se o texto realmente transborda
        if (bioText.scrollHeight > alturaContraida) {
            bioBtn.style.display = 'block'; // Mostra o botão

            // 3. Adiciona o evento de clique
            bioBtn.addEventListener('click', () => {
                if (bioContainer.classList.toggle('expanded')) {
                    bioBtn.textContent = 'Ler Menos';
                } else {
                    bioBtn.textContent = 'Ler Mais';
                    // Opcional: rola para o topo da bio ao fechar
                    // bioContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
        }
        atualizarLimiteCheckboxes();

        // Listener para atualizar o campo hidden com a forma de pagamento selecionada
        document.querySelectorAll('input[name="forma_pagamento"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('tipo_pagamento_backend').value = this.value;
            });
        });

        
        const formConfirmacao = document.getElementById('form-confirmacao');
        if (formConfirmacao) {
            formConfirmacao.addEventListener('submit', submeterAgendamento);
        }
        
        // --- INÍCIO DA LÓGICA DO NOVO MODAL DE TERMOS (Baseado no usuário) ---
        
        // Elementos do Formulário Principal (Ponte)
        const formTermsCheckbox = document.getElementById('termos-aceitos');
        const formTermsLabel = document.getElementById('label-termos-aceitos');

        // Elementos do Modal (IDs do HTML fornecido)
        const openBtn = document.getElementById('openTerms'); // ID do botão no form
        const overlay = document.getElementById('termsOverlay');
        const modalCheckbox = document.getElementById('acceptCheckbox');
        const modalConfirmBtn = document.getElementById('confirmBtn');
        const modalCancelBtn = document.getElementById('cancelBtn');

        if (openBtn && overlay && modalCheckbox && modalConfirmBtn && modalCancelBtn && formTermsCheckbox && formTermsLabel) {
            
            // Abrir modal
            openBtn.addEventListener('click', (e) => {
                e.preventDefault(); // Impedir que o botão (type="button") submeta o form
                overlay.style.display = 'flex';
                // Sincroniza o modal com o form
                modalCheckbox.checked = formTermsCheckbox.checked;
                modalConfirmBtn.disabled = !formTermsCheckbox.checked;
                modalCheckbox.focus();
            });

            // Fechar modal (Cancelar)
            modalCancelBtn.addEventListener('click', () => {
                overlay.style.display = 'none';
                
                // Se o usuário fechar, desmarcamos a checkbox do modal
                // mas mantemos o estado da checkbox principal (ele pode ter aceito antes)
                modalCheckbox.checked = formTermsCheckbox.checked;
                modalConfirmBtn.disabled = !formTermsCheckbox.checked;
                
                openBtn.focus();
            });

            // Habilitar botão "Concordo"
            modalCheckbox.addEventListener('change', () => {
                modalConfirmBtn.disabled = !modalCheckbox.checked;
            });

            // Confirmar (Ação principal)
            modalConfirmBtn.addEventListener('click', () => {
                if (!modalCheckbox.checked) return;
                
                // 1. Marca a checkbox oculta do formulário principal
                formTermsCheckbox.checked = true;
                
                // 2. Atualiza o label no formulário principal
                formTermsLabel.textContent = 'Termos de pagamento aceitos.';
                formTermsLabel.style.color = 'green';
                
                // 3. Fecha o modal
                overlay.style.display = 'none';
                openBtn.focus();

                // Dispara o evento customizado (como no exemplo do user)
                window.dispatchEvent(new CustomEvent('terms:accepted'));
            });

            // Fechar modal com tecla ESC
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && overlay.style.display === 'flex') {
                    modalCancelBtn.click();
                }
            });

            // Clicar no label do formulário também abre o modal
            formTermsLabel.addEventListener('click', () => {
                openBtn.click();
            });

        } else {
            console.error("Erro: Elementos do modal de termos não foram encontrados. A funcionalidade de aceite de termos está quebrada.");
        }
        // --- FIM DA LÓGICA DO NOVO MODAL DE TERMOS ---
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

        const formData = new FormData(form);
        formData.append('tipo_pagamento', selectedPayment === 'cartao' ? 'credito' : selectedPayment); 
        
        try {
            const response = await fetch('/backend/agendamentos/salvar', {
                method: 'POST',
                body: new URLSearchParams(formData) 
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