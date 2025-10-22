document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('agendamento-container');
    const loadingMessage = document.getElementById('loading-message');

    const params = new URLSearchParams(window.location.search);
    const profissionalId = params.get('id');

    let profissionalData = null; // Guardar dados do profissional
    let dataSelecionada = null; // Manter a data selecionada
    let horarioSelecionado = null; // Manter o horário selecionado

    if (!profissionalId) {
        loadingMessage.textContent = 'Erro: ID do Profissional não fornecido na URL.';
        loadingMessage.style.color = 'red';
        return;
    }

    // --- FUNÇÃO PARA PEGAR A DATA DE HOJE NO FORMATO YYYY-MM-DD ---
    function getHojeFormatado() {
        const hoje = new Date();
        const ano = hoje.getFullYear();
        const mes = String(hoje.getMonth() + 1).padStart(2, '0'); // Meses são 0-indexed
        const dia = String(hoje.getDate()).padStart(2, '0');
        return `${ano}-${mes}-${dia}`;
    }

    async function carregarDadosIniciais() {
        loadingMessage.textContent = 'Carregando informações do profissional...';
        container.innerHTML = ''; // Limpa container enquanto carrega

        try {
            const resProfissional = await fetch(`/backend/profissionais/detalhe/${profissionalId}`);

            if (!resProfissional.ok) {
                const errorData = await resProfissional.json().catch(() => ({ message: 'Profissional não encontrado ou indisponível.' }));
                throw new Error(errorData.message);
            }

            profissionalData = await resProfissional.json();

            // Renderiza a parte inicial da página (infos do prof + seletor de data)
            renderizarLayoutBase(profissionalData);
            loadingMessage.style.display = 'none'; // Esconde msg de loading
            carregarAvaliacoesProfissional(profissionalId);

            // --- CARREGA HORÁRIOS DE HOJE AUTOMATICAMENTE ---
            const hoje = getHojeFormatado();
            const dateInput = document.getElementById('data-consulta');
            if(dateInput) {
                dateInput.value = hoje; // Define o valor do input para hoje
                dataSelecionada = hoje; // Define a data selecionada
                buscarHorariosDisponiveis(); // Busca os horários para hoje
            }
            // --- FIM DA MODIFICAÇÃO ---

        } catch (error) {
            console.error('Erro ao carregar dados do profissional:', error);
            container.innerHTML = `<p id="loading-message" style="color: red; text-align: center;">${error.message || 'Não foi possível carregar as informações. Tente novamente.'}</p>`;
        }
    }

    function renderizarLayoutBase(prof) {
        const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
        const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`;
        const fotoFinal = prof.img_profissional ? `/${prof.img_profissional}` : fotoUrlPadrao;
        const hojeFormatado = getHojeFormatado(); // Pega a data de hoje para o input

        const conteudoHTML = `
            <h2 class="titulo-aba-prof">Seu agendamento com ${prof.nome_usuario.split(' ')[0]}</h2>

            <div class="agendamento-detalhe-grid">
                 <div class="profissional-info-coluna">
                    <h3>${prof.nome_usuario}</h3>
                    <img src="${fotoFinal}" alt="${prof.nome_usuario}" class="profissional-info-foto">
                    <h4>Especialidades</h4>
                    <ul class="especialidades-lista">
                        ${(prof.especialidade || 'N/A').split(',').map(e => `<li>${e.trim()}</li>`).join('')}
                    </ul>
                    <h4>Duração e Tipo de Atendimento</h4>
                    <ul class="tipos-lista">
                        <li><img src="img/icons/relogio.svg" class="icon-prof"> Sessão com duração de 50min</li>
                        <li><img src="img/icons/adulto.svg" class="icon-prof"> Adultos</li>
                        <li><img src="img/icons/casais.svg" class="icon-prof"> Casais</li>
                    </ul>
                </div>
                <div class="profissional-bio-coluna">
                    <h4>Um pouco sobre ${prof.nome_usuario.split(' ')[0]}</h4>
                    <p>${prof.sobre || 'Biografia não disponível.'}</p>
                    <h4>Avaliações de Clientes</h4>
<div class="avaliacoes-detalhe" id="avaliacoes-detalhe-${prof.id_profissional}">
    <span class="nota-media-detalhe">Carregando avaliações...</span>
    <div class="estrelas-media-detalhe">☆☆☆☆☆</div>
    <span class="total-avaliacoes-detalhe"></span>
</div>

                    <div class="valor-consulta-box">
                        Valor da Consulta: <strong>R$ ${parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ',')}</strong>
                    </div>
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
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-grupo-duplo">
                        <div class="form-grupo">
                            <label for="telefone">Telefone (com DDD):</label>
                            <input type="tel" id="telefone" name="telefone" required>
                        </div>
                        <div class="form-grupo">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf" required>
                        </div>
                    </div>
                     <div class="form-grupo-duplo">
                        <div class="form-grupo">
                            <label>Leia os Termos</label>
                            <a href="termos.html" target="_blank" class="termos-link">Termos e Condições</a>
                            <div class="checkbox-container">
                                <input type="checkbox" id="termos" name="termos" required>
                                <label for="termos">Li e concordo</label>
                            </div>
                        </div>
                 
                    </div>
                     <div class="form-rodape">
                        <div class="valor-sinal">
                            Valor do sinal: <strong>R$ ${parseFloat(prof.sinal_consulta || 0).toFixed(2).replace('.', ',')}</strong>
                         </div>
                      
                        <button type="submit" class="btn-pagamento" id="btn-confirmar-agendamento">Solicitar Agendamento</button>
                    </div>
                     <p id="agendamento-status-message" style="margin-top: 15px; text-align: center; font-weight: bold;"></p>
                </form>
            </div>
        `;

        container.innerHTML = conteudoHTML;

        // Adiciona listener para o input de data APÓS renderizar
        const dateInput = document.getElementById('data-consulta');
        if (dateInput) {
            dateInput.addEventListener('change', buscarHorariosDisponiveis);
        }

        // Adiciona listener para o formulário de confirmação
        const formConfirmacao = document.getElementById('form-confirmacao');
        if (formConfirmacao) {
            formConfirmacao.addEventListener('submit', submeterAgendamento);
        }
    }

    // --- Função buscarHorariosDisponiveis agora pega a data do input ---
    async function buscarHorariosDisponiveis() {
        const dateInput = document.getElementById('data-consulta');
        dataSelecionada = dateInput.value; // Pega a data ATUAL do input
        const horariosContainer = document.getElementById('agenda-horarios-container');
        const agendaBody = horariosContainer.querySelector('.agenda-body');
        const feedbackEl = document.getElementById('agenda-feedback');
        const confirmacaoContainer = document.getElementById('confirmacao-container');

        // Limpa estado anterior
        agendaBody.innerHTML = '';
        agendaBody.style.display = 'none';
        feedbackEl.textContent = 'Buscando horários...';
        feedbackEl.style.color = '#faf6ee';
        horarioSelecionado = null; // Reseta horário
        confirmacaoContainer.style.display = 'none'; // Esconde confirmação

        if (!dataSelecionada) {
            feedbackEl.textContent = 'Selecione uma data válida.';
            return;
        }

        // --- Adiciona validação para datas passadas ---
        const hoje = getHojeFormatado();
        if (dataSelecionada < hoje) {
             feedbackEl.textContent = 'Não é possível agendar para datas passadas.';
             feedbackEl.style.color = '#ffdddd';
             return;
        }
        // --- Fim validação data passada ---

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
                agendaBody.style.display = 'grid'; // Mostra grid se houver horários
            } else {
                feedbackEl.textContent = 'Nenhum horário disponível para esta data.';
            }

        } catch (error) {
            console.error('Erro ao buscar horários:', error);
            feedbackEl.textContent = error.message || 'Erro ao carregar horários.';
            feedbackEl.style.color = '#ffdddd'; // Cor de erro
        }
    }

    // --- Função renderizarBotoesHorario (sem alterações) ---
    function renderizarBotoesHorario(horarios, container) {
        container.innerHTML = ''; // Limpa antes de adicionar
         // Agrupa horários por período (Manhã, Tarde) - Opcional
        const horariosManha = horarios.filter(h => parseInt(h.split(':')[0]) < 13);
        const horariosTarde = horarios.filter(h => parseInt(h.split(':')[0]) >= 13);

        let htmlBotoes = '<div class="agenda-coluna">'; // Manhã
        if (horariosManha.length > 0) {
             htmlBotoes += horariosManha.map(hora => `<button class="horario-btn" data-horario="${hora}">${hora}</button>`).join('');
        } else {
             htmlBotoes += '<p style="font-size: 0.9em; text-align: center;">Sem horários<br>na manhã</p>';
        }
         htmlBotoes += '</div><div class="agenda-coluna">'; // Tarde
         if (horariosTarde.length > 0) {
             htmlBotoes += horariosTarde.map(hora => `<button class="horario-btn" data-horario="${hora}">${hora}</button>`).join('');
         } else {
             htmlBotoes += '<p style="font-size: 0.9em; text-align: center;">Sem horários<br>à tarde</p>';
         }
         htmlBotoes += '</div>';

        container.innerHTML = htmlBotoes;


        // Adiciona listeners aos botões criados
        container.querySelectorAll('.horario-btn').forEach(btn => {
            btn.addEventListener('click', () => selecionarHorario(btn));
        });
    }

    // --- Função selecionarHorario (sem alterações) ---
    function selecionarHorario(botaoClicado) {
        const container = botaoClicado.closest('.agenda-body');
        // Remove a classe 'selecionado' de todos os botões
        container.querySelectorAll('.horario-btn').forEach(btn => btn.classList.remove('selecionado'));

        // Adiciona a classe ao botão clicado
        botaoClicado.classList.add('selecionado');
        horarioSelecionado = botaoClicado.dataset.horario;

        // Mostra a seção de confirmação e atualiza o texto
        const confirmacaoContainer = document.getElementById('confirmacao-container');
        const textoConfirmacao = document.getElementById('horario-confirmacao-texto');
        const dataInputHidden = document.getElementById('data_selecionada_hidden');
        const horarioInputHidden = document.getElementById('horario_selecionado_hidden');


        if (dataSelecionada && horarioSelecionado) {
             const dataFormatada = new Date(dataSelecionada + 'T00:00:00').toLocaleDateString('pt-BR', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' });
            textoConfirmacao.textContent = `Agendar para: ${dataFormatada} às ${horarioSelecionado}`;
            dataInputHidden.value = dataSelecionada; // YYYY-MM-DD
            horarioInputHidden.value = horarioSelecionado; // HH:MM
            confirmacaoContainer.style.display = 'block'; // Mostra o formulário
        } else {
             confirmacaoContainer.style.display = 'none'; // Esconde se algo der errado
        }
    }

    // --- Função submeterAgendamento (sem alterações) ---
     async function submeterAgendamento(event) {
        event.preventDefault(); // Impede envio tradicional
        const form = event.target;
        const submitButton = document.getElementById('btn-confirmar-agendamento');
        const statusMessage = document.getElementById('agendamento-status-message');

        statusMessage.textContent = 'Solicitando agendamento...';
        statusMessage.style.color = '#faf6ee';
        submitButton.disabled = true;

        const formData = new FormData(form);

        try {
            const response = await fetch('/backend/agendamentos/salvar', {
                method: 'POST',
                body: new URLSearchParams(formData) // Envia como form-urlencoded
            });

            const result = await response.json();

            if (response.ok && result.success) {
                statusMessage.textContent = result.message || 'Agendamento solicitado! Redirecionando...';
                statusMessage.style.color = 'lightgreen';
                form.reset(); // Limpa o formulário
                 document.getElementById('agenda-horarios-container').querySelector('.agenda-body').innerHTML = ''; // Limpa horários
                 document.getElementById('confirmacao-container').style.display = 'none'; // Esconde confirmação
                // Redirecionar para pagamento ou página de sucesso após um tempo
                 setTimeout(() => {
                     // window.location.href = result.redirectPagamento || '/meus-agendamentos'; // Exemplo
                     window.location.href = '/index.html'; // Volta para a home por enquanto
                 }, 3000);
            } else {
                throw new Error(result.message || `Erro ${response.status}`);
            }

        } catch (error) {
            console.error('Erro ao submeter agendamento:', error);
            statusMessage.textContent = `Erro: ${error.message}. Tente novamente.`;
            statusMessage.style.color = 'red';
             // Se o erro foi conflito de horário, busca novamente
             if (error.message.includes('horário acabou de ser reservado')) {
                 buscarHorariosDisponiveis(); // Atualiza a lista de horários
             }
        } finally {
            submitButton.disabled = false;
        }
    }


    // Inicia o carregamento da página
    carregarDadosIniciais();
});
async function carregarAvaliacoesProfissional(idProf) {
    const containerAvaliacoes = document.getElementById(`avaliacoes-detalhe-${idProf}`);
    if (!containerAvaliacoes) return; // Sai se o container não for encontrado

    // Seleciona os elementos dentro do container específico
    const notaMediaEl = containerAvaliacoes.querySelector('.nota-media-detalhe');
    const estrelasEl = containerAvaliacoes.querySelector('.estrelas-media-detalhe');
    const totalEl = containerAvaliacoes.querySelector('.total-avaliacoes-detalhe');

    // Limpa estado inicial
    notaMediaEl.textContent = 'Carregando...';
    estrelasEl.innerHTML = "☆☆☆☆☆";
    totalEl.textContent = '';

    try {
        // Usa o endpoint que já existe para buscar avaliações por ID do profissional
        const response = await fetch(`/backend/avaliacoes?id=${idProf}`);
        if (!response.ok) throw new Error('Falha ao buscar avaliações');

        const comentarios = await response.json();

        if (!comentarios || comentarios.length === 0) {
            notaMediaEl.textContent = 'Sem avaliações';
            totalEl.textContent = '(0 avaliações)';
            return; // Mantém estrelas vazias
        }

        // Calcula a média e formata as estrelas
        const total = comentarios.length;
        const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota_avaliacao), 0); //
        const media = soma_notas / total;
        const media_arredondada = Math.round(media); // Arredonda para preencher estrelas

        notaMediaEl.textContent = `${media.toFixed(1)} de 5`; // Exibe média com uma casa decimal
        estrelasEl.innerHTML = `${"★".repeat(media_arredondada)}${"☆".repeat(5 - media_arredondada)}`;
        totalEl.textContent = `(${total} ${total === 1 ? 'avaliação' : 'avaliações'})`;

    } catch (error) {
        console.error('Erro ao carregar avaliações:', error);
        notaMediaEl.textContent = 'Erro ao carregar';
        totalEl.textContent = ''; // Limpa total em caso de erro
    }
}