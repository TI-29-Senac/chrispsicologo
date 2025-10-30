document.addEventListener('DOMContentLoaded', () => {
    const loadingMessage = document.getElementById('loading-message');
    const conteudoPagamento = document.getElementById('conteudo-pagamento');
    const statusMessage = document.getElementById('pagamento-status-message');
    const formCartao = document.getElementById('form-cartao');
    const btnPagar = document.getElementById('btn-pagar-cartao');
    
    // Elementos de dados
    const profissionalNomeEl = document.getElementById('profissional-nome');
    const dataHoraEl = document.getElementById('data-hora');
    const valorSinalEl = document.getElementById('valor-sinal');

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

            // Formata data e hora (já corrigido para DATETIME)
            const dataAgendamento = new Date(data.agendamento.data_agendamento.replace(' ', 'T'));
            const dataFormatada = dataAgendamento.toLocaleDateString('pt-BR', {
                day: '2-digit', month: 'long', year: 'numeric', timeZone: 'UTC'
            });
            const horaFormatada = dataAgendamento.toLocaleTimeString('pt-BR', {
                hour: '2-digit', minute: '2-digit', timeZone: 'UTC'
            });

            // Preenche os dados na tela
            profissionalNomeEl.textContent = data.profissional.nome_usuario;
            dataHoraEl.textContent = `${dataFormatada} às ${horaFormatada}`;
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

    // ==========================================================
    // CORREÇÃO: A função foi movida para DENTRO do 'DOMContentLoaded'
    // ==========================================================
    /**
     * Função para simular o pagamento com cartão
     */
    async function processarPagamentoCartao(event) {
        event.preventDefault(); // Impede o envio real do formulário
        
        statusMessage.textContent = 'Processando pagamento...';
        statusMessage.style.color = '#faf6ee';
        btnPagar.disabled = true;
        btnPagar.textContent = 'Processando...';

        try {
            // 1. Simula a chamada para um gateway de pagamento
            await new Promise(resolve => setTimeout(resolve, 2000)); // Simula 2s
            
            // 2. Confirma em nosso backend
            const response = await fetch(`/backend/agendamentos/confirmar-sinal/${agendamentoId}`, {
                method: 'POST'
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Não foi possível confirmar o pagamento.');
            }

            // Sucesso
            statusMessage.textContent = 'Pagamento aprovado! Redirecionando para sua conta...';
            statusMessage.style.color = 'lightgreen';

            setTimeout(() => {
                window.location.href = '/minha-conta.html'; // Redireciona para a conta do usuário
            }, 2500);

        } catch (error) {
            console.error('Erro ao processar pagamento:', error);
            statusMessage.textContent = `Erro: ${error.message}. Tente novamente.`;
            statusMessage.style.color = 'red';
            btnPagar.disabled = false;
            btnPagar.textContent = 'Pagar Sinal';
        }
    }
    // ==========================================================
    // FIM DA CORREÇÃO
    // ==========================================================


    // Adiciona o listener ao formulário
    // Agora esta linha (102) funciona, pois a função está visível
    formCartao.addEventListener('submit', processarPagamentoCartao);

    // Carrega os dados iniciais
    carregarDetalhesAgendamento();

    // Máscaras simples (opcional, mas melhora a UX)
    document.getElementById('numero-cartao').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    });
    document.getElementById('validade-cartao').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(\d{2})(\d{1,2})/, '$1/$2').trim();
    });

}); // Fim do 'DOMContentLoaded'