// chrispsicologo/js/profissionais.js

// Lógica do Carrossel (Início)
document.addEventListener('DOMContentLoaded', () => {
    const cardsContainerCarrossel = document.getElementById("cards-carrossel");

    if (cardsContainerCarrossel) {
        fetch('/backend/profissionais/listar-publico')
            .then(response => response.json())
            .then(profissionais => {
                if (!profissionais || profissionais.length === 0) return;

                const profissionaisDuplicados = [];
                for (let i = 0; i < 5; i++) {
                    profissionais.forEach(item => profissionaisDuplicados.push(item));
                }

                profissionaisDuplicados.forEach(item => {
                    const card = document.createElement("div");
                    card.className = "card";

                    // --- LÓGICA DE IMAGEM AUTOMÁTICA ---
                    const nomeBase = item.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `img/profissionais/${nomeBase}.png`;

                    card.innerHTML = `
                        <a href="profissionais.html" class="card-link">
                            <div class="foto" style="background-image: url(${item.img_profissional || fotoUrlPadrao}); background-size: cover; background-position: center;"></div>
                            <h3>${item.nome_usuario}</h3>
                            <div class="avaliacoes"><h4>Psicólogo(a)</h4><p>${item.especialidade}</p></div>
                        </a>`;
                    cardsContainerCarrossel.appendChild(card);
                });
            })
            .catch(error => console.error('Erro ao carregar profissionais para o carrossel:', error));
    }
});


// Lógica da Página Profissionais.html (Principal)
document.addEventListener("DOMContentLoaded", () => {
    const containerProfissionais = document.getElementById("container-profissionais");

    if (containerProfissionais) {
        fetch('/backend/profissionais/listar-publico')
            .then(response => {
                if (!response.ok) throw new Error('A resposta da rede não foi OK');
                return response.json();
            })
            .then(profissionais => {
                if (!profissionais || profissionais.length === 0) {
                    containerProfissionais.innerHTML = '<p style="text-align: center; font-size: 1.2rem;">Nenhum profissional encontrado no momento.</p>';
                    return;
                }
                
                const profissionaisMapeado = profissionais.map(prof => {
                    // --- LÓGICA DE IMAGEM AUTOMÁTICA ---
                    const nomeBase = prof.nome_usuario.split(' ')[0].toLowerCase();
                    const fotoUrlPadrao = `img/profissionais/${nomeBase}.png`;
                    
                    return {
                        id_db: prof.id_profissional,
                        nome: prof.nome_usuario,
                        foto: prof.img_profissional || fotoUrlPadrao,
                        sobre: prof.sobre || '',
                        valor: parseFloat(prof.valor_consulta || 0).toFixed(2).replace('.', ','),
                        especialidades: prof.especialidade ? prof.especialidade.split(',').map(s => s.trim()) : ["Clínica Geral"],
                        duracao: "Sessão com duração de 50min",
                        tipos: [
                            { icone: "img/icons/adulto.svg", texto: "Adultos" },
                            { icone: "img/icons/casais.svg", texto: "Casais" }
                        ]
                    };
                });

                renderizarCardsProfissionais(profissionaisMapeado);
            })
            .catch(error => {
                console.error('Erro ao carregar a lista de profissionais:', error);
                containerProfissionais.innerHTML = '<p style="color: red; text-align: center;">Ocorreu um erro ao carregar os profissionais. Tente novamente mais tarde.</p>';
            });
    }
});

// O restante do arquivo (funções renderizarCardsProfissionais, fetchAndRenderProfStats, etc.) permanece o mesmo da versão anterior.
// Omitido para brevidade, pois não precisa de alterações.

function renderizarCardsProfissionais(profissionais) {
    const container = document.getElementById("container-profissionais");
    container.innerHTML = '';

    profissionais.forEach((prof, index) => {
        const card = document.createElement("div");
        card.classList.add("profissional");
        
        card.innerHTML = `
          <div class="card-prof">
            <h3>${prof.nome}</h3>
            <div class="foto"><img src="${prof.foto}" alt="${prof.nome}"></div>
            <div class="avaliacoes-prof" data-index="${index}" data-id-db="${prof.id_db}">
              <div class="estrelas-media-container">
                <span class="total-avaliacoes">... Avaliações</span>
                <div class="estrelas-media" id="estrelas-prof-${prof.id_db}">☆☆☆☆☆</div>
                <span class="nota-media" id="nota-media-prof-${prof.id_db}">0.0</span>
              </div>
            </div>
            <div class="valor">Valor da Consulta: R$${prof.valor}</div> 
            <a href="agendamento-detalhe.html?id=${prof.id_db}" class="btn">Agenda Disponível</a>
          </div>

          <div class="detalhes">
          <h4>Sobre Mim</h4>
          <p style="font-size: 1.1rem; line-height: 1.4;">${prof.sobre}</p>
            <h4>Especialidades</h4>
            <ul class="especialidades-lista">${prof.especialidades.map(e => `<li>${e}</li>`).join("")}</ul>
            <h4 class="espaco-prof">Duração e Tipo de Atendimento</h4>
            <div class="tipos">
              <ul>
                <li><img src="img/icons/relogio.svg" class="icon-prof"> ${prof.duracao}</li>
                ${prof.tipos.map(t => `<li><img src="${t.icone}" class="icon-prof"> ${t.texto}</li>`).join("")}
              </ul>
            </div>
          </div>`;
        container.appendChild(card);
    });

    fetchAndRenderProfStats(profissionais);
    adicionarListenersModal(profissionais);
}

async function fetchAndRenderProfStats(profissionais) {
    for (const prof of profissionais) {
        const id_db = prof.id_db;
        const totalAvaliacoesEl = document.querySelector(`.avaliacoes-prof[data-id-db="${id_db}"] .total-avaliacoes`);
        const notaMediaEl = document.getElementById(`nota-media-prof-${id_db}`);
        const estrelasEl = document.getElementById(`estrelas-prof-${id_db}`);
        
        if (!totalAvaliacoesEl || !notaMediaEl || !estrelasEl) continue;

        try {
            const response = await fetch(`/backend/avaliacoes?id=${id_db}`);
            const comentarios = await response.json();
            prof.avaliacoes = comentarios;

            if (!comentarios || comentarios.length === 0) {
                totalAvaliacoesEl.textContent = '0 Avaliações';
                notaMediaEl.textContent = '0.0';
                estrelasEl.innerHTML = "☆☆☆☆☆";
                continue;
            }

            const total = comentarios.length;
            const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota_avaliacao), 0);
            const media = soma_notas / total;
            const media_arredondada = Math.round(media);
            
            totalAvaliacoesEl.textContent = `${total} Avaliações`;
            notaMediaEl.textContent = media.toFixed(1);
            estrelasEl.innerHTML = `${"★".repeat(media_arredondada)}${"☆".repeat(5 - media_arredondada)}`;
        } catch (error) {
            totalAvaliacoesEl.textContent = 'N/A';
        }
    }
}

function adicionarListenersModal(profissionais) {
    const modal = document.getElementById("modal-avaliacoes");
    
    document.querySelectorAll(".avaliacoes-prof").forEach(el => {
        el.addEventListener("click", () => {
            const index = el.dataset.index;
            const profData = profissionais[index];
            renderizarModal(profData.avaliacoes || [], profData.nome);
            modal.style.display = "flex";
        });
    });

    modal.addEventListener("click", e => {
        if (e.target === modal || e.target.classList.contains('fechar-modal')) {
            modal.style.display = "none";
        }
    });
}

function renderizarModal(comentarios, nomeProfissional) {
    const modal = document.getElementById("modal-avaliacoes");
    const soma_notas = comentarios.reduce((acc, c) => acc + parseFloat(c.nota_avaliacao), 0);
    const total = comentarios.length;
    const media = total > 0 ? (soma_notas / total).toFixed(1) : "0.0";
    
    const modalConteudo = modal.querySelector('.modal-conteudo');
    modalConteudo.innerHTML = `
      <span class="fechar-modal">&times;</span>
      <h4>Avaliações de ${nomeProfissional} (${media} de 5.0 - ${total} Avaliações)</h4>
      <div id="barras-container-api"></div>
      <div class="comentarios" id="comentarios-lista-api"></div>
    `;

    const barrasContainer = modalConteudo.querySelector("#barras-container-api");
    const contagem = {1:0, 2:0, 3:0, 4:0, 5:0};
    comentarios.forEach(c => {
        const n = Math.round(c.nota_avaliacao);
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

    const comentariosLista = modalConteudo.querySelector("#comentarios-lista-api");
    if (total === 0) {
        comentariosLista.innerHTML = `<p style="text-align: center; margin-top: 30px;">Nenhuma avaliação encontrada.</p>`;
    } else {
        comentarios.forEach(c => {
            const comentarioEl = document.createElement("div");
            comentarioEl.className = "comentario";
            comentarioEl.innerHTML = `
                <div class="comentario-estrelas">${"★".repeat(Math.round(c.nota_avaliacao))}${"☆".repeat(5 - Math.round(c.nota_avaliacao))}</div>
                <p>"${c.descricao_avaliacao}" - <strong>${c.cliente || 'Anônimo'}</strong></p>
            `;
            comentariosLista.appendChild(comentarioEl);
        });
    }
}