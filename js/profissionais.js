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
                     return; 
                 }

                if (profissionais.length === 0) return; 

                // --- Lógica para Duplicação (para carrossel infinito) ---
                const profissionaisParaCarrossel = [];
                const repeticoes = Math.max(3, Math.ceil(10 / profissionais.length)); 
                for (let i = 0; i < repeticoes; i++) {
                    profissionais.forEach(item => profissionaisParaCarrossel.push(item));
                }
                // --- Fim da Lógica para Duplicação ---


                profissionaisParaCarrossel.forEach(item => {
                    const card = document.createElement("div");
                    card.className = "card"; 

                    const nomeBase = item.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`; 
                    const fotoFinal = item.img_profissional ? `/${item.img_profissional}` : fotoUrlPadrao; 

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

                 if (typeof destacarCardCentral === 'function') {
                    const cardWidth = 300 + 60; 
                    const totalOriginalCards = profissionais.length;
                    cardsContainerCarrossel.scrollLeft = totalOriginalCards * cardWidth;
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

    const defaultTipos = [
        { icone: "img/icons/adulto.svg", texto: "Adultos" }
    ];

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

                const profissionaisMapeado = profissionais.map(prof => {
                    const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `/img/profissionais/${nomeBase}.png`; 
                    const fotoFinal = prof.img_profissional ? `/${prof.img_profissional}` : fotoUrlPadrao; 
                    
                    // --- (INÍCIO) CORREÇÃO APLICADA AQUI ---
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

                    return {
                        id_db: prof.id_profissional,
                        nome: prof.nome_usuario,
                        foto: fotoFinal,
                        sobre: prof.sobre || '',
                        valor: parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ','),
                        especialidades: prof.especialidade ? prof.especialidade.split(',').map(s => s.trim()) : ["Clínica Geral"],
                        duracao: "Sessão com duração de 50min",
                        tipos: tiposDeAtendimento, // <<< USA A VARIÁVEL CORRIGIDA
                        avaliacoes: []
                    };
                });

                renderizarCardsProfissionais(profissionaisMapeado);
            })
            .catch(error => {
                console.error('Erro ao carregar a lista de profissionais:', error);
                if (containerProfissionais) {
                    containerProfissionais.innerHTML = `<p style="color: red; text-align: center;">Ocorreu um erro ao carregar os profissionais: ${error.message}. Tente novamente mais tarde.</p>`;
                }
            });
    }
});

// Função para renderizar os cards na página profissionais.html
function renderizarCardsProfissionais(profissionais) {
    const container = document.getElementById("container-profissionais");
    if (!container) return;
    container.innerHTML = ''; 

    profissionais.forEach((prof, index) => {
        const card = document.createElement("div");
        card.classList.add("profissional"); 

        const maxChars = 130; 
        let sobreResumido = prof.sobre || '';
        if (sobreResumido.length > maxChars) {
            sobreResumido = sobreResumido.substring(0, maxChars).trim() + '...'; 
        }

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

    fetchAndRenderProfStats(profissionais);
    adicionarListenersModal(profissionais);
}

// Função para buscar e renderizar estatísticas de avaliação nos cards
async function fetchAndRenderProfStats(profissionais) {
    for (const prof of profissionais) {
        const id_db = prof.id_db;
        const cardElement = document.querySelector(`.profissional .card-prof [data-id-db="${id_db}"]`);
        if (!cardElement) continue; 

        const totalAvaliacoesEl = cardElement.querySelector('.total-avaliacoes');
        const notaMediaEl = document.getElementById(`nota-media-prof-${id_db}`); 
        const estrelasEl = document.getElementById(`estrelas-prof-${id_db}`); 

        if (!totalAvaliacoesEl || !notaMediaEl || !estrelasEl) continue; 

        try {
            const response = await fetch(`/backend/avaliacoes?id=${id_db}`);
            if (!response.ok) throw new Error(`Falha ao buscar avaliações para prof ${id_db}`);

            const comentarios = await response.json();
             prof.avaliacoes = comentarios || [];

            if (!comentarios || comentarios.length === 0) {
                totalAvaliacoesEl.textContent = '0 Avaliações';
                notaMediaEl.textContent = '0.0';
                estrelasEl.innerHTML = "☆☆☆☆☆";
                continue;
            }

            const total = comentarios.length;
            const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota_avaliacao || 0), 0);
            const media = total > 0 ? soma_notas / total : 0;
            const media_arredondada = Math.round(media); 

            totalAvaliacoesEl.textContent = `${total} ${total === 1 ? 'Avaliação' : 'Avaliações'}`;
            notaMediaEl.textContent = media.toFixed(1); 
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
    if (!modal) return; 

    document.querySelectorAll(".avaliacoes-prof").forEach(el => {
        el.addEventListener("click", () => {
            const index = parseInt(el.dataset.index, 10); 
            if (!isNaN(index) && profissionais[index]) {
                const profData = profissionais[index];
                renderizarModal(profData.avaliacoes || [], profData.nome); 
                modal.style.display = "flex"; 
            } else {
                 console.error("Índice de profissional inválido ou não encontrado:", el.dataset.index);
            }
        });
    });

    modal.addEventListener("click", e => {
        if (e.target === modal || e.target.classList.contains('fechar-modal') || e.target.closest('.fechar-modal')) {
            modal.style.display = "none"; 
        }
    });
}

// Função para renderizar o conteúdo do modal de avaliações
function renderizarModal(comentarios, nomeProfissional) {
    const modal = document.getElementById("modal-avaliacoes");
    if (!modal) return;

    const total = comentarios.length;
    const soma_notas = comentarios.reduce((acc, c) => acc + parseFloat(c.nota_avaliacao || 0), 0);
    const media = total > 0 ? (soma_notas / total).toFixed(1) : "0.0";

    const modalConteudo = modal.querySelector('.modal-conteudo');
    if (!modalConteudo) return; 

    modalConteudo.innerHTML = `
      <span class="fechar-modal" id="fechar-modal-interno">&times;</span>
      <h4>Avaliações de ${nomeProfissional} (${media} de 5.0 - ${total} ${total === 1 ? 'Avaliação' : 'Avaliações'})</h4>
      <div id="barras-container-api"></div>
      <div class="comentarios" id="comentarios-lista-api"></div>
    `;

    const barrasContainer = modalConteudo.querySelector("#barras-container-api");
    if (barrasContainer) {
        const contagem = {1:0, 2:0, 3:0, 4:0, 5:0}; 
        comentarios.forEach(c => {
            const n = Math.round(parseFloat(c.nota_avaliacao || 0));
            if (n >= 1 && n <= 5) contagem[n]++;
        });

        for (let nota = 5; nota >= 1; nota--) { 
            const linha = document.createElement("div");
            linha.classList.add("linha-nota"); 
            linha.innerHTML = `
                <span class="numero-1">${nota} ★</span>
                <div class="barra-1"><div class="barra-2" style="width: ${total > 0 ? (contagem[nota] / total) * 100 : 0}%;"></div></div>
                <span class="qtd-nota">${contagem[nota]}</span>
            `;
            barrasContainer.appendChild(linha);
        }
    }

    const comentariosLista = modalConteudo.querySelector("#comentarios-lista-api");
    if (comentariosLista) {
        if (total === 0) {
            comentariosLista.innerHTML = `<p style="text-align: center; margin-top: 30px;">Nenhuma avaliação encontrada para este profissional.</p>`;
        } else {
            comentarios.forEach(c => {
                const comentarioEl = document.createElement("div");
                comentarioEl.className = "comentario"; 
                const notaArredondada = Math.round(parseFloat(c.nota_avaliacao || 0));
                comentarioEl.innerHTML = `
                    <div class="comentario-estrelas">${"★".repeat(notaArredondada)}${"☆".repeat(5 - notaArredondada)}</div>
                    <p>"${c.descricao_avaliacao || 'Sem comentário.'}" - <strong>${c.cliente || 'Anônimo'}</strong></p>
                `;
                comentariosLista.appendChild(comentarioEl);
            });
        }
    }
}