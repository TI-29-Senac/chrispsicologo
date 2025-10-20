document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('agendamento-container');
    const loadingMessage = document.getElementById('loading-message');

    const params = new URLSearchParams(window.location.search);
    const profissionalId = params.get('id');

    if (!profissionalId) {
        loadingMessage.textContent = 'Erro: Profissional não encontrado.';
        loadingMessage.style.color = 'red';
        return;
    }

    async function carregarDadosDaPagina() {
        try {
            // Busca os dados do profissional e os horários em paralelo
            const [resProfissional, resHorarios] = await Promise.all([
                fetch(`/backend/profissionais/detalhe/${profissionalId}`),
                fetch(`/backend/profissionais/horarios/${profissionalId}`)
            ]);

            if (!resProfissional.ok) throw new Error('Profissional não encontrado.');
            if (!resHorarios.ok) throw new Error('Não foi possível carregar os horários.');

            const profissional = await resProfissional.json();
            const horarios = await resHorarios.json();

            // Renderiza a página com todos os dados
        renderizarPaginaCompleta(profissional, horarios);

        } catch (error) {
            console.error('Erro ao carregar dados da página:', error);
            loadingMessage.textContent = 'Não foi possível carregar as informações. Tente novamente mais tarde.';
            loadingMessage.style.color = 'red';
        }
    }

    function renderizarPaginaCompleta(prof, horarios) {
        container.innerHTML = ''; 

        const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
        const fotoUrlPadrao = `img/profissionais/${nomeBase}.png`;
        const fotoFinal = prof.img_profissional || fotoUrlPadrao;

        // --- ESTRUTURA HTML CORRIGIDA ---
        const conteudoHTML = `
            <h2 class="titulo-aba-prof">Seu agendamento</h2>
            
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
                    <div class="valor-consulta-box">
                        Valor da Consulta: <strong>R$ ${parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ',')}</strong>
                    </div>
                </div>
            </div>

            <div class="agenda-container">
                <div class="agenda-tabela">
                    <div class="agenda-header">
                        ${Object.keys(horarios).map(dia => `<div>${dia}</div>`).join('')}
                    </div>
                    <div class="agenda-body">
                        ${Object.keys(horarios).map(dia => `
                            <div class="agenda-coluna">
                                ${horarios[dia].map(hora => `<button class="horario-btn">${hora}</button>`).join('')}
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>

            <div class="confirmacao-container">
                <h3>Confirmação</h3>
                <form id="form-confirmacao" class="confirmacao-form">
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
                        <div class="form-grupo">
                            <label for="forma-pagamento">Forma de Pagamento</label>
                            <select id="forma-pagamento" name="forma-pagamento">
                                <option value="pix">Pix</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-rodape">
                        <div class="valor-sinal">
                            Valor do sinal: <strong>R$ ${parseFloat(prof.sinal_consulta || 0).toFixed(2).replace('.', ',')}</strong>
                        </div>
                        <button type="submit" class="btn-pagamento">Ir para o pagamento</button>
                    </div>
                </form>
            </div>
        `;

        container.innerHTML = conteudoHTML;
    }

    carregarDadosDaPagina();
});