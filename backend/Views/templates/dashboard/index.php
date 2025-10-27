<?php



$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Utilizador';
?>

<div class="w3-container">
    <h2 style="color: #5D6D68;">👋 Bem-vindo(a) ao Painel, <?= htmlspecialchars($nomeUsuario)  ?>!</h2>
    <p>Utilize o menu lateral para navegar pelas secções administrativas.</p>
    <hr>

    
        </div>
    <h3 style="color: #5D6D68;">Análise dos Últimos 6 Meses</h3>

    <div class="w3-row-padding" style="margin: 0 -16px;">
        
        <div class="w3-half" style="margin-bottom: 24px;">
            <div class="w3-card-4 w3-white" style="border-radius: 12px;">
                <div class="w3-container" style="padding-bottom: 24px;">
                    <h4 style="color: #5D6D68;">Agendamentos por Mês</h4>
                    <canvas id="graficoAgendamentos" style="width:100%; max-height:300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="w3-half" style="margin-bottom: 24px;">
            <div class="w3-card-4 w3-white" style="border-radius: 12px;">
                <div class="w3-container" style="padding-bottom: 24px;">
                    <h4 style="color: #5D6D68;">Novos Clientes por Mês</h4>
                    <canvas id="graficoNovosClientes" style="width:100%; max-height:300px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="w3-row-padding" style="margin: 0 -16px;">
        <div class="w3-container" style="margin-bottom: 24px;"> <div class="w3-card-4 w3-white" style="border-radius: 12px;">
                <div class="w3-container" style="padding-bottom: 24px;">
                    <h4 style="color: #5D6D68;">Faturamento por Mês (R$)</h4>
                    <canvas id="graficoFaturamento" style="width:100%; max-height:300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    </div> <script>
document.addEventListener("DOMContentLoaded", () => {
    
    // Configuração padrão dos gráficos
    const barChartConfig = (labels, data, label) => ({
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(93, 109, 104, 0.7)', // Cor #5D6D68 com transparência
                borderColor: 'rgba(93, 109, 104, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                         // Garante que o eixo Y tenha apenas números inteiros se os dados forem inteiros
                        precision: 0 
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Oculta a legenda, já que o título do card já explica
                }
            }
        }
    });

    // 1. Gráfico de Agendamentos
    try {
        const ctxAgendamentos = document.getElementById('graficoAgendamentos').getContext('2d');
        const labelsAgendamentos = <?= $chartAgendamentosLabels ?? '[]' ?>;
        const dataAgendamentos = <?= $chartAgendamentosValores ?? '[]' ?>;
        new Chart(ctxAgendamentos, barChartConfig(labelsAgendamentos, dataAgendamentos, 'Agendamentos'));
    } catch (e) {
        console.error("Erro ao renderizar gráfico de agendamentos:", e);
    }

    // 2. Gráfico de Novos Clientes
    try {
        const ctxNovosClientes = document.getElementById('graficoNovosClientes').getContext('2d');
        const labelsNovosClientes = <?= $chartNovosClientesLabels ?? '[]' ?>;
        const dataNovosClientes = <?= $chartNovosClientesValores ?? '[]' ?>;
        new Chart(ctxNovosClientes, barChartConfig(labelsNovosClientes, dataNovosClientes, 'Novos Clientes'));
    } catch (e) {
        console.error("Erro ao renderizar gráfico de novos clientes:", e);
    }
    
    // 3. Gráfico de Faturamento
    try {
        const ctxFaturamento = document.getElementById('graficoFaturamento').getContext('2d');
        const labelsFaturamento = <?= $chartFaturamentoLabels ?? '[]' ?>;
        const dataFaturamento = <?= $chartFaturamentoValores ?? '[]' ?>;
        
        const configFaturamento = barChartConfig(labelsFaturamento, dataFaturamento, 'Faturamento (R$)');
        
        // Sobrescreve a opção 'precision' para permitir decimais (R$)
        configFaturamento.options.scales.y.ticks.precision = 2; 

        new Chart(ctxFaturamento, configFaturamento);
    } catch (e) {
        console.error("Erro ao renderizar gráfico de faturamento:", e);
    }

});
</script>
    </div> </div>