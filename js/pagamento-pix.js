document.addEventListener('DOMContentLoaded', () => {
    const loadingMessage = document.getElementById('loading-message');
    const conteudoPagamento = document.getElementById('conteudo-pagamento');
    const statusMessage = document.getElementById('pagamento-status-message');
    
    // Elementos de dados
    const profissionalNomeEl = document.getElementById('profissional-nome');
    const dataHoraEl = document.getElementById('data-hora');
    const valorSinalEl = document.getElementById('valor-sinal');
    const btnConfirmar = document.getElementById('btn-confirmar-pix');
    const btnCopiar = document.getElementById('btn-copiar-pix');
    const pixInput = document.getElementById('pix-codigo');

    const params = new URLSearchParams(window.location.search);
    const agendamentoId = params.get('id');

    if (!agendamentoId) {
        loadingMessage.textContent = 'Erro: ID do Agendamento não fornecido. Volte e tente novamente.';
        loadingMessage.style.color = 'red';
        return;
    }

    // Função para carregar detalhes do agendamento
    async function carregarDetalhesAgendamento() {
        try {
            const response = await fetch(`/backend/agendamentos/detalhe-pagamento/${agendamentoId}`);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Não foi possível buscar os dados do agendamento.');
            }

            const data = await response.json();

            // --- CORREÇÃO APLICADA AQUI ---
            // 'data.agendamento.data_agendamento' agora é um DATETIME (ex: "2025-10-30 14:00:00")
            // Precisamos converter o espaço em 'T' para o construtor Date entender corretamente
            const dataAgendamento = new Date(data.agendamento.data_agendamento.replace(' ', 'T'));

            const dataFormatada = dataAgendamento.toLocaleDateString('pt-BR', {
                day: '2-digit', month: 'long', year: 'numeric',
                timeZone: 'UTC' // Adiciona timeZone para evitar problemas de fuso
            });
            const horaFormatada = dataAgendamento.toLocaleTimeString('pt-BR', {
                hour: '2-digit', minute: '2-digit',
                timeZone: 'UTC' // Adiciona timeZone para evitar problemas de fuso
            });
            // --- FIM DA CORREÇÃO ---

            // Preenche os dados na tela
            profissionalNomeEl.textContent = data.profissional.nome_usuario;
            dataHoraEl.textContent = `${dataFormatada} às ${horaFormatada}`; // Junta data e hora
            valorSinalEl.textContent = `R$ ${parseFloat(data.agendamento.valor_sinal).toFixed(2).replace('.', ',')}`;
            
            // Exibe o conteúdo
            loadingMessage.style.display = 'none';
            conteudoPagamento.style.display = 'block';

        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            loadingMessage.textContent = `Erro: ${error.message}`;
            loadingMessage.style.color = 'red';
        }
    }

    // Função para simular a confirmação do pagamento
    async function confirmarPagamento() {
        statusMessage.textContent = 'Confirmando pagamento...';
        statusMessage.style.color = '#faf6ee';
        btnConfirmar.disabled = true;
        btnConfirmar.textContent = 'Processando...';

        try {
            // Este endpoint irá atualizar o status do agendamento/pagamento no DB
            const response = await fetch(`backend/agendamentos/confirmar-sinal/${agendamentoId}`, {
                method: 'POST'
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Não foi possível confirmar o pagamento.');
            }

            // Sucesso
            statusMessage.textContent = 'Pagamento confirmado! Redirecionando para sua conta...';
            statusMessage.style.color = 'lightgreen';

            setTimeout(() => {
                window.location.href = '/minha-conta.html'; // Redireciona para a conta do usuário
            }, 2500);

        } catch (error) {
            console.error('Erro ao confirmar pagamento:', error);
            statusMessage.textContent = `Erro: ${error.message}`;
            statusMessage.style.color = 'red';
            btnConfirmar.disabled = false;
            btnConfirmar.textContent = 'Já paguei, confirmar agendamento';
        }
    }

    // Botão de Copiar
    btnCopiar.addEventListener('click', () => {
        pixInput.select();
        document.execCommand('copy');
        btnCopiar.textContent = 'Copiado!';
        setTimeout(() => {
            btnCopiar.textContent = 'Copiar';
        }, 1500);
    });

    // Adiciona o listener ao botão principal
    btnConfirmar.addEventListener('click', confirmarPagamento);

    // Carrega os dados iniciais
    carregarDetalhesAgendamento();
});