// chrispsicologo/js/profissionais.js

// Lógica do Carrossel (Início) - Seção presente no index.html
document.addEventListener('DOMContentLoaded', () => {
    const cardsContainerCarrossel = document.getElementById("cards-carrossel");

    if (cardsContainerCarrossel) {
        fetch('/backend/profissionais/listar-publico')
            .then(response => {
                if (!response.ok) throw new Error('A resposta da rede não foi OK ao carregar para carrossel');
                return response.json();
             })
            .then(profissionais => {
                if (!profissionais || !Array.isArray(profissionais)) {
                     console.warn('Dados dos profissionais para carrossel inválidos ou vazios.');
                     return; // Sai se não houver dados válidos
                 }

                if (profissionais.length === 0) return; // Sai se não houver profissionais

                // --- Lógica para Duplicação (para carrossel infinito) ---
                const profissionaisParaCarrossel = [];
                const repeticoes = Math.max(3, Math.ceil(10 / profissionais.length)); // Garante pelo menos uns 10 cards, repetindo 3x no mínimo
                for (let i = 0; i < repeticoes; i++) {
                    profissionais.forEach(item => profissionaisParaCarrossel.push(item));
                }
                // --- Fim da Lógica para Duplicação ---


                // Itera sobre a lista DUPLICADA para criar os cards
                profissionaisParaCarrossel.forEach(item => {
                    const card = document.createElement("div");
                    card.className = "card"; // Classe original do card do carrossel

                    // --- CORREÇÃO APLICADA AQUI ---
                    const nomeBase = item.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`; // Caminho absoluto
                    const fotoFinal = item.img_profissional ? `/${item.img_profissional}` : fotoUrlPadrao; // Caminho absoluto

                    // --- USA TAG IMG ---
                    card.innerHTML = `
                        <a href="profissionais.html" class="card-link">
                            <div class="foto">
                               <img src="${fotoFinal}" alt="${item.nome_usuario}" style="width:100%; height:100%; object-fit: cover; border-radius: inherit;">
                            </div>
                            <h3>${item.nome_usuario}</h3>
                            <div class="avaliacoes"><h4>Psicólogo(a)</h4><p>${item.especialidade || 'Clínica Geral'}</p></div>
                        </a>`;
                    cardsContainerCarrossel.appendChild(card);
                });

                 // Inicializa lógica de destaque e scroll após adicionar cards
                 if (typeof destacarCardCentral === 'function') {
                    // Ajuste inicial do scroll para o meio após duplicar
                    const cardWidth = 300 + 60; // Largura do card + gap
                    const totalOriginalCards = profissionais.length;
                    // Começa no início do segundo bloco de cards
                    cardsContainerCarrossel.scrollLeft = totalOriginalCards * cardWidth;
                    // Chama o destaque inicial
                    destacarCardCentral();
                 }


            })
            .catch(error => console.error('Erro ao carregar profissionais para o carrossel:', error));
    }
});
// Fim da Lógica do Carrossel (index.html)


// Lógica da Página Profissionais.html (Principal)
document.addEventListener("DOMContentLoaded", () => {
    const containerProfissionais = document.getElementById("container-profissionais");

    // Só executa o fetch se estiver na página de profissionais
    if (containerProfissionais) {
        fetch('/backend/profissionais/listar-publico')
            .then(response => {
                if (!response.ok) throw new Error('A resposta da rede não foi OK ao listar profissionais');
                return response.json();
            })
            .then(profissionais => {
                 if (!profissionais || !Array.isArray(profissionais)) {
                     throw new Error('Dados dos profissionais inválidos ou vazios.');
                 }

                if (profissionais.length === 0) {
                    containerProfissionais.innerHTML = '<p style="text-align: center; font-size: 1.2rem;">Nenhum profissional encontrado no momento.</p>';
                    return;
                }

                // --- NOVO PASSO 1: Criar um mapa de tipos de atendimento por ID ---
                // Você pode encontrar os IDs de cada profissional no seu painel de admin.
                const tiposPorProfissional = {
                    // Exemplo para Chris (ID 6)
                    6: [ 
                        { icone: "img/icons/adulto.svg", texto: "Adultos" },
                        { icone: "img/icons/casais.svg", texto: "Casais" }
                    ],
                    // Exemplo para Carla (ID 7)
                    7: [
                        { icone: "img/icons/crianca.svg", texto: "Infantil" },
                        { icone: "img/icons/adolescente.svg", texto: "Adolescentes" }
                    ],
                    // Exemplo para Helena (ID 8)
                    8: [
                        { icone: "img/icons/idosos.svg", texto: "Idosos" },
                        { icone: "img/icons/familiar.svg", texto: "Terapia Familiar" }
                    ],
                    // Exemplo para Luana (ID 9)
                    9: [
                        { icone: "img/icons/publico_feminino.svg", texto: "Mulheres" },
                        { icone: "img/icons/maternidade.svg", texto: "Maternidade" }
                    ],
                    // Exemplo para Yan (ID 10)
                    10: [
                        { icone: "img/icons/jovem_adulto.svg", texto: "Jovens Adultos" },
                        { icone: "img/icons/online.svg", texto: "Atendimento Online" }
                    ],
                    // Adicione mais IDs e tipos aqui...
                    29: [
                        { icone: "img/icons/jovem_adulto.svg", texto: "Jovens Adultos" },
                        { icone: "img/icons/online.svg", texto: "Atendimento Online" }
                    ]
                };
                
                // --- NOVO PASSO 2: Criar um conjunto de tipos padrão ---
                // Usado para profissionais novos que você ainda não adicionou ao mapa acima.
                const defaultTipos = [
                    { icone: "img/icons/adulto.svg", texto: "Adultos" }
                ];

                // Mapeia os dados da API para o formato esperado pelo frontend
                const profissionaisMapeado = profissionais.map(prof => {
                    // --- CORREÇÃO APLICADA AQUI ---
                    const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`; // Caminho absoluto
                    const fotoFinal = prof.img_profissional ? `/${prof.img_profissional}` : fotoUrlPadrao; // Caminho absoluto
                    
                    // --- NOVO PASSO 3: Buscar os tipos corretos ---
                    const idProf = prof.id_profissional;
                    const tiposDeAtendimento = tiposPorProfissional[idProf] || defaultTipos; // Usa os tipos do ID ou o padrão

                    return {
                        id_db: idProf,
                        nome: prof.nome_usuario,
                        foto: fotoFinal, // Usa o caminho corrigido
                        sobre: prof.sobre || '',
                        valor: parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ','),
                        especialidades: prof.especialidade ? prof.especialidade.split(',').map(s => s.trim()) : ["Clínica Geral"],
                        duracao: "Sessão com duração de 50min",
                        
                        // --- NOVO PASSO 4: Usar a variável ---
                        tipos: tiposDeAtendimento, // <-- ANTES ESTAVA FIXO
                        
                        avaliacoes: [] // Inicializa vazio, será preenchido depois
                    };
                });

                renderizarCardsProfissionais(profissionaisMapeado);
            })
            .catch(error => {
                console.error('Erro ao carregar a lista de profissionais:', error);
                if (containerProfissionais) { // Verifica se ainda existe antes de modificar
                    containerProfissionais.innerHTML = `<p style="color: red; text-align: center;">Ocorreu um erro ao carregar os profissionais: ${error.message}. Tente novamente mais tarde.</p>`;
                }
            });
    }
});
// Função para renderizar os cards na página profissionais.html
function renderizarCardsProfissionais(profissionais) {
    const container = document.getElementById("container-profissionais");
    if (!container) return; // Sai se o container não existir
    container.innerHTML = ''; // Limpa o container

    profissionais.forEach((prof, index) => {
        const card = document.createElement("div");
        card.classList.add("profissional"); // Classe principal do card na pág. profissionais

        const maxChars = 130; // Limite de caracteres para o "Sobre Mim"
        let sobreResumido = prof.sobre || '';
        if (sobreResumido.length > maxChars) {
            sobreResumido = sobreResumido.substring(0, maxChars).trim() + '...'; // Trunca e adiciona "..."
        }

        // --- CORREÇÃO APLICADA AQUI (USA TAG IMG) ---
        card.innerHTML = `
          <div class="card-prof">
            <h3>${prof.nome}</h3>
            <div class="foto"><img src="${prof.foto}" alt="${prof.nome}" style="width:100%; height:100%; object-fit: cover; border-radius: inherit;"></div>
            <div class="avaliacoes-prof" data-index="${index}" data-id-db="${prof.id_db}">
              <div class="estrelas-media-container">
                <span class="total-avaliacoes">... Avaliações</span>
                <div class="estrelas-media" id="estrelas-prof-${prof.id_db}">☆☆☆☆☆</div>
                <span class="nota-media" id="nota-media-prof-${prof.id_db}">0.0</span>
              </div>
            </div>
            <div class="valor">Consulta: R$${prof.valor}</div>
            
          </div>

          <div class="detalhes">
         <h4>Especialidades</h4>
            <ul class="especialidades-lista">${prof.especialidades.slice(0, 5).map(e => `<li>${e}</li>`).join("")}</ul>
            <h4 class="espaco-prof">Duração e Tipo de Atendimento</h4>
            <div class="tipos">
              <ul>
                <li><img src="img/icons/relogio.svg" class="icon-prof" alt="Ícone relógio"> ${prof.duracao}</li>
                ${prof.tipos.map(t => `<li><img src="${t.icone}" class="icon-prof" alt=""> ${t.texto}</li>`).join("")}
              </ul>
            </div>
            <a href="agendamento-detalhe.html?id=${prof.id_db}" class="btn">Agendar</a>
          </div>`;
        container.appendChild(card);
    });

    // Busca e renderiza as estatísticas de avaliação DEPOIS de criar os cards
    fetchAndRenderProfStats(profissionais);
    // Adiciona listeners para abrir o modal de avaliações
    adicionarListenersModal(profissionais);
}

// Função para buscar e renderizar estatísticas de avaliação nos cards
async function fetchAndRenderProfStats(profissionais) {
    for (const prof of profissionais) {
        const id_db = prof.id_db;
        // Seleciona os elementos DENTRO do card específico
        const cardElement = document.querySelector(`.profissional .card-prof [data-id-db="${id_db}"]`);
        if (!cardElement) continue; // Pula se o container do card não for encontrado

        const totalAvaliacoesEl = cardElement.querySelector('.total-avaliacoes');
        const notaMediaEl = document.getElementById(`nota-media-prof-${id_db}`); // IDs são únicos
        const estrelasEl = document.getElementById(`estrelas-prof-${id_db}`); // IDs são únicos

        if (!totalAvaliacoesEl || !notaMediaEl || !estrelasEl) continue; // Pula se algum elemento interno não for encontrado

        try {
            // Busca avaliações para este profissional
            const response = await fetch(`/backend/avaliacoes?id=${id_db}`);
            if (!response.ok) throw new Error(`Falha ao buscar avaliações para prof ${id_db}`);

            const comentarios = await response.json();
             // Armazena os comentários no objeto do profissional para usar no modal
             prof.avaliacoes = comentarios || [];

            if (!comentarios || comentarios.length === 0) {
                totalAvaliacoesEl.textContent = '0 Avaliações';
                notaMediaEl.textContent = '0.0';
                estrelasEl.innerHTML = "☆☆☆☆☆";
                continue; // Próximo profissional
            }

            // Calcula a média
            const total = comentarios.length;
            const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota_avaliacao || 0), 0);
            const media = total > 0 ? soma_notas / total : 0;
            const media_arredondada = Math.round(media); // Para preencher estrelas

            // Atualiza os elementos no DOM
            totalAvaliacoesEl.textContent = `${total} ${total === 1 ? 'Avaliação' : 'Avaliações'}`;
            notaMediaEl.textContent = media.toFixed(1); // Exibe com uma casa decimal
            estrelasEl.innerHTML = `${"★".repeat(media_arredondada)}${"☆".repeat(5 - media_arredondada)}`;
        } catch (error) {
            console.error(`Erro ao buscar stats para prof ${id_db}:`, error);
            if (totalAvaliacoesEl) totalAvaliacoesEl.textContent = 'Erro ao carregar';
             if (notaMediaEl) notaMediaEl.textContent = 'N/A';
             if (estrelasEl) estrelasEl.innerHTML = "☆☆☆☆☆";
        }
    }
}

// Função para adicionar listeners aos botões/áreas que abrem o modal
function adicionarListenersModal(profissionais) {
    const modal = document.getElementById("modal-avaliacoes");
    if (!modal) return; // Sai se o modal não existir

    // Seleciona todas as áreas clicáveis de avaliação nos cards
    document.querySelectorAll(".avaliacoes-prof").forEach(el => {
        el.addEventListener("click", () => {
            const index = parseInt(el.dataset.index, 10); // Converte para número
            // Verifica se o índice é válido e se existe no array
            if (!isNaN(index) && profissionais[index]) {
                const profData = profissionais[index];
                renderizarModal(profData.avaliacoes || [], profData.nome); // Passa avaliações (ou array vazio) e nome
                modal.style.display = "flex"; // Mostra o modal
            } else {
                 console.error("Índice de profissional inválido ou não encontrado:", el.dataset.index);
            }
        });
    });

    // Listener para fechar o modal (clicando fora ou no X)
    modal.addEventListener("click", e => {
        // Verifica se o clique foi no overlay (fundo) ou no botão de fechar
        if (e.target === modal || e.target.classList.contains('fechar-modal') || e.target.closest('.fechar-modal')) {
            modal.style.display = "none"; // Esconde o modal
        }
    });
}

// Função para renderizar o conteúdo do modal de avaliações
function renderizarModal(comentarios, nomeProfissional) {
    const modal = document.getElementById("modal-avaliacoes");
    if (!modal) return;

    // Calcula média e total (redundante, mas garante consistência)
    const total = comentarios.length;
    const soma_notas = comentarios.reduce((acc, c) => acc + parseFloat(c.nota_avaliacao || 0), 0);
    const media = total > 0 ? (soma_notas / total).toFixed(1) : "0.0";

    const modalConteudo = modal.querySelector('.modal-conteudo');
    if (!modalConteudo) return; // Sai se a estrutura interna do modal não for encontrada

    // Limpa e recria o conteúdo interno do modal
    modalConteudo.innerHTML = `
      <span class="fechar-modal" id="fechar-modal-interno">&times;</span>
      <h4>Avaliações de ${nomeProfissional} (${media} de 5.0 - ${total} ${total === 1 ? 'Avaliação' : 'Avaliações'})</h4>
      <div id="barras-container-api"></div>
      <div class="comentarios" id="comentarios-lista-api"></div>
    `;

    // Renderiza as barras de progresso das notas
    const barrasContainer = modalConteudo.querySelector("#barras-container-api");
    if (barrasContainer) {
        const contagem = {1:0, 2:0, 3:0, 4:0, 5:0}; // Contagem por nota
        comentarios.forEach(c => {
            const n = Math.round(parseFloat(c.nota_avaliacao || 0));
            if (n >= 1 && n <= 5) contagem[n]++;
        });

        for (let nota = 5; nota >= 1; nota--) { // De 5 para 1 estrela
            const linha = document.createElement("div");
            linha.classList.add("linha-nota"); // Classe para estilização
            linha.innerHTML = `
                <span class="numero-1">${nota} ★</span>
                <div class="barra-1"><div class="barra-2" style="width: ${total > 0 ? (contagem[nota] / total) * 100 : 0}%;"></div></div>
                <span class="qtd-nota">${contagem[nota]}</span>
            `;
            barrasContainer.appendChild(linha);
        }
    }

    // Renderiza a lista de comentários individuais
    const comentariosLista = modalConteudo.querySelector("#comentarios-lista-api");
    if (comentariosLista) {
        if (total === 0) {
            comentariosLista.innerHTML = `<p style="text-align: center; margin-top: 30px;">Nenhuma avaliação encontrada para este profissional.</p>`;
        } else {
            comentarios.forEach(c => {
                const comentarioEl = document.createElement("div");
                comentarioEl.className = "comentario"; // Classe para estilização
                const notaArredondada = Math.round(parseFloat(c.nota_avaliacao || 0));
                comentarioEl.innerHTML = `
                    <div class="comentario-estrelas">${"★".repeat(notaArredondada)}${"☆".repeat(5 - notaArredondada)}</div>
                    <p>"${c.descricao_avaliacao || 'Sem comentário.'}" - <strong>${c.cliente || 'Anônimo'}</strong></p>
                `;
                comentariosLista.appendChild(comentarioEl);
            });
        }
    }

    // Adiciona listener ao novo botão de fechar (se necessário, embora o listener no modal já deva funcionar)
    // const fecharInterno = modalConteudo.querySelector("#fechar-modal-interno");
    // if (fecharInterno) {
    //     fecharInterno.addEventListener("click", () => {
    //         modal.style.display = "none";
    //     });
    // }
}