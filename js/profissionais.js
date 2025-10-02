// chrispsicologo/js/profissionais.js

// Lógica do Carrossel (Início)
document.addEventListener('DOMContentLoaded', () => {
    // Apenas a estrutura dos cards do carrossel (índex.html)
    const profissionaisCarrossel = [
      { "foto": "img/profissionais/chris.png", "nome": "Chris Nunes", "profissao": "Psicólogo", "especialidade": "Formado em Psicologia PCD e Inclusão Social" },
      { "foto": "img/profissionais/yan.png", "nome": "Yan Nakamura", "profissao": "Psicólogo", "especialidade": "Formado em Psicoterapia Infantil e Adolescente"},
      { "foto": "img/profissionais/luana.png", "nome": "Luana Duarte", "profissao": "Terapeuta", "especialidade": "Pós-graduada em Psicologia Clínica Existencial e Gestáltica"},
      { "foto": "img/profissionais/carla.png", "nome": "Carla Dias","profissao": "Psicóloga", "especialidade": "Pós-graduada em Neurociência e Psicologia Aplicada"},
      { "foto": "img/profissionais/helena.png", "nome": "Helena Costa", "profissao": "Terapeuta", "especialidade": "Formado em Terapia Cognitiva Comportamental"}
    ];

    const profissionaisDuplicados = [];
    var repeticoes = 5;
    for (let i = 0; i < repeticoes; i++) {
      profissionaisCarrossel.forEach((item) => {
        profissionaisDuplicados.push(item);
      });}
        
  
    const cardsContainer = document.getElementById("cards-carrossel");
  
    
    const totalReps = 5;
    for (let i = 0; i < totalReps; i++) {
      profissionaisDuplicados.forEach((item) => {
        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
          <a href="contato.html" class="card-link">
            <div class="foto" style="background-image: url(${item.foto}); background-size: cover; background-position: center;"></div>
            <h3>${item.nome}</h3>
            <div class="avaliacoes"><h4>${item.profissao}</h4><p>${item.especialidade}</p></div>
          </a>`;
        cardsContainer.appendChild(card);
      });
    }
  });


// Lógica da Página Profissionais.html (Principal)
document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("container-profissionais");

  // Adicionamos o id_db para mapear ao banco de dados (Assumimos 1 a 5)
  const profissionais = [
  {
    id_db: 6, 
    nome: "Chris Nunes",
    foto: "img/profissionais/chris.png",
    valor: "260,00",
    especialidades: [
      "Psicoterapia Individual",
      "Relacionamentos",
      "Psicologia do Envelhecimento",
      "Ansiedade e Depressão em Adultos",
      "Luto e Perdas",
      "Conflitos Profissionais e Familiares",
      "Comunicação e Mediação de Conflitos"
    ],
    duracao: "Sessão com duração de 60min",
    tipos: [
      { icone: "img/icons/old-man.svg", texto: "Idosos" },
      { icone: "img/icons/casais.svg", texto: "Casais" },
      { icone: "img/icons/adulto.svg", texto: "Adultos" }
    ],
    // Removidas as avaliações hardcoded
  },
  {
    id_db: 7, 
    nome: "Helena Costa",
    foto: "img/profissionais/helena.png",
    valor: "280,00",
    especialidades: [
      "Psicoterapia para Mulheres",
      "Ansiedade, Depressão e Estresse em Mulheres",
      "Autoestima e Autoconhecimento",
      "Relacionamentos Afetivos e Abusivos",
      "Luto, Perdas e Ciclos de Vida",
      "Psicologia da Maternidade e Pós-Parto",
      "Apoio Psicológico na Gravidez e Planejamento Familiar"
    ],
    duracao: "Sessão com duração de 50min",
    tipos: [
      { icone: "img/icons/mulheres.svg", texto: "Mulheres" },
      { icone: "img/icons/publico_feminino.svg", texto: "Abordagens Femininas" },
      { icone: "img/icons/maternidade.svg", texto: "Maternidade" }
    ],
  },
  {
    id_db: 8, 
    nome: "Carla Dias",
    foto: "img/profissionais/carla.png",
    valor: "240,00",
    especialidades: [
      "Psicoterapia Individual",
      "Terapia de Casal e Família",
      "Ansiedade e Depressão em Adultos",
      "Psicologia do Envelhecimento",
      "Estresse Ocupacional e Burnout",
      "Luto e Processos de Perda",
      "Desenvolvimento de Habilidades Sociais"
    ],
    duracao: "Sessão com duração de 45min",
    tipos: [
      { icone: "img/icons/adulto.svg", texto: "Adultos" },
      { icone: "img/icons/old-man.svg", texto: "Idosos" },
      { icone: "img/icons/casais.svg", texto: "Casais" }
    ],
  },
  {
    id_db: 9, 
    nome: "Luana Duarte",
    foto: "img/profissionais/luana.png",
    valor: "300,00",
    especialidades: [
      "Psicoterapia Infantil",
      "Orientação Parental",
      "Transtornos de Aprendizagem e Desenvolvimento",
      "Ansiedade e Autoestima em Crianças e Adolescentes",
      "Conflitos Escolares e Bullying",
      "Orientação em Relacionamentos e Sexualidade na Adolescência",
      "Luto e Adaptação a Mudanças"
    ],
    duracao: "Sessão com duração de 55min",
    tipos: [
      { icone: "img/icons/crianca.svg", texto: "Crianças" },
      { icone: "img/icons/adolescente.svg", texto: "Adolescentes" }
    ],
  },
  {
    id_db: 10, 
    nome: "Yan Nakamura",
    foto: "img/profissionais/yan.png",
    valor: "270,00",
    especialidades: [
      "Psicoterapia para Jovens Adultos e Adolescentes",
      "Orientação Vocacional e Profissional",
      "Ansiedade, Autoestima e Identidade",
      "Relacionamentos Interpessoais",
      "Luto e Mudanças de Ciclo de Vida",
      "Sexualidade e Questões de Gênero",
      "Psicoterapia Familiar e Mediação de Conflitos"
    ],
    duracao: "Sessão com duração de 60min",
    tipos: [
      { icone: "img/icons/adulto.svg", texto: "Jovens Adultos" },
      { icone: "img/icons/adolescente.svg", texto: "Adolescentes" },
      { icone: "img/icons/familia.svg", texto: "Família" }
    ],
  }
];


  profissionais.forEach((prof, index) => {
    const card = document.createElement("div");
    card.classList.add("profissional");
    // Placeholders iniciais, que serão atualizados abaixo
    const nota_media_placeholder = 0.0; 
    const qtde_avaliacoes_placeholder = 0; 
    
    // Adicionamos o 'data-id-db' no elemento de avaliações
    card.innerHTML = `
      <div class="card-prof">
        <h3>${prof.nome}</h3>
        <div class="foto"><img src="${prof.foto}" alt="${prof.nome}"></div>

        <div class="avaliacoes-prof" data-index="${index}" data-id-db="${prof.id_db}">
          <div class="estrelas-media-container">
            <span class="total-avaliacoes">${qtde_avaliacoes_placeholder} Avaliações</span>
            <div class="estrelas-media" id="estrelas-prof-${prof.id_db}">
              ${"★".repeat(Math.round(nota_media_placeholder))}${"☆".repeat(5-Math.round(nota_media_placeholder))}
            </div>
            <span class="nota-media" id="nota-media-prof-${prof.id_db}">${nota_media_placeholder.toFixed(1)}</span>
          </div>
        </div>

        <div class="valor">Valor da Consulta: R$${prof.valor}</div> 
        <a href="agendar.html" class="btn">Agenda Disponível</a>
      </div>

      <div class="detalhes">
        <h4>Especialidades</h4>
        <ul class="especialidades-lista">
          ${prof.especialidades.map(e => `<li>${e}</li>`).join("")}
        </ul>

        <h4 class="espaco-prof">Duração e Tipo de Atendimento</h4>
        <div class="tipos">
          <ul>
            <li><img src="img/icons/relogio.svg" class="icon-prof"> ${prof.duracao}</li>
            ${prof.tipos.map(t => `
              <li><img src="${t.icone}" class="icon-prof"> ${t.texto}</li>
            `).join("")}
          </ul>
        </div>
      </div>
    `;
    container.appendChild(card);
  });
  
  
  // ==========================================================
  // NOVA FUNÇÃO: BUSCAR DADOS DE AVALIAÇÃO PARA O DISPLAY INICIAL
  // ==========================================================
  async function fetchAndRenderProfStats(profissionais) {
      for (const prof of profissionais) {
          const id_db = prof.id_db;
          const totalAvaliacoesEl = document.querySelector(`.avaliacoes-prof[data-id-db="${id_db}"] .total-avaliacoes`);
          const notaMediaEl = document.getElementById(`nota-media-prof-${id_db}`);
          const estrelasEl = document.getElementById(`estrelas-prof-${id_db}`);
          
          if (!totalAvaliacoesEl || !notaMediaEl || !estrelasEl) continue;

          try {
              const response = await fetch(`/backend/avaliacoes?id=${id_db}`);
              if (!response.ok) throw new Error('Erro ao buscar estatísticas');

              const comentarios = await response.json();
              const total = comentarios.length;
              
              if (total === 0) {
                  totalAvaliacoesEl.textContent = '0 Avaliações';
                  notaMediaEl.textContent = (0.0).toFixed(1);
                  estrelasEl.innerHTML = "☆☆☆☆☆";
                  prof.avaliacoes = []; // Guarda a lista vazia para o modal
                  continue;
              }

              // Calcular a média
              const soma_notas = comentarios.reduce((acc, curr) => acc + parseFloat(curr.nota), 0);
              const media = soma_notas / total;
              const media_arredondada = Math.round(media);
              
              // Atualizar a UI
              totalAvaliacoesEl.textContent = `${total} Avaliações`;
              notaMediaEl.textContent = media.toFixed(1);
              estrelasEl.innerHTML = `${"★".repeat(media_arredondada)}${"☆".repeat(5 - media_arredondada)}`;
              
              // Armazenar os comentários diretamente no objeto do profissional para uso no modal
              prof.avaliacoes = comentarios;

          } catch (error) {
              console.error(`Erro ao carregar stats do Prof ID ${id_db}:`, error);
              totalAvaliacoesEl.textContent = 'Erro'; // Feedback de erro
          }
      }
  }

  // Chama a função para carregar as estatísticas na inicialização
  fetchAndRenderProfStats(profissionais);
  
  
  // ==========================================================
  // LÓGICA DO MODAL (REUTILIZANDO DADOS JÁ BUSCADOS)
  // ==========================================================

  // Função para renderizar o conteúdo do modal
  function renderizarModal(comentarios, nomeProfissional) {
    let soma_notas = 0;
    const contagem = {1:0,2:0,3:0,4:0,5:0};
    
    comentarios.forEach(c => {
      const n = Math.round(c.nota);
      if (n >= 1 && n <= 5) contagem[n]++;
      soma_notas += parseFloat(c.nota);
    });

    const total = comentarios.length;
    const media = total > 0 ? (soma_notas / total).toFixed(1) : 0.0;
    
    // Estrutura base do modal
    const modalConteudo = modal.querySelector('.modal-conteudo');
    modalConteudo.innerHTML = `
      <span class="fechar-modal" id="fechar-modal-api">&times;</span>
      <h4>Avaliações de ${nomeProfissional} (${media} de 5.0 - ${total} Avaliações)</h4>
      <div id="barras-container-api"></div>
      <div id="comentarios-lista-api"></div>
    `;
    const barrasContainer = document.getElementById("barras-container-api");
    const comentariosLista = document.getElementById("comentarios-lista-api");

    // Reassocia o evento de fechar o modal
    document.getElementById("fechar-modal-api").addEventListener("click", () => modal.style.display = "none");
    
    // Renderização das Barras
    for (let nota = 5; nota >= 1; nota--) {
      const linha = document.createElement("div");
      linha.classList.add("linha-nota");

      linha.innerHTML = `
        <span class="numero-1">${nota}</span>
        <div class="barra-1">
          <div class="barra-2" style="width: ${total > 0 ? (contagem[nota] / total) * 100 : 0}%;"></div>
        </div>
        <span class="qtd-nota">${contagem[nota]} Avaliações</span>
      `;
      barrasContainer.appendChild(linha);
    }

    // Renderização dos Comentários
    if (total === 0) {
      comentariosLista.innerHTML = `<p style="text-align: center; margin-top: 30px;">Nenhuma avaliação encontrada para este profissional.</p>`;
    } else {
      comentarios.forEach(c => {
        const comentarioEl = document.createElement("div");
        comentarioEl.className = "comentario";
        comentarioEl.innerHTML = `
          <div class="comentario-estrelas">
            ${"★".repeat(Math.round(c.nota))}${"☆".repeat(5 - Math.round(c.nota))}
          </div>
          <p>${c.comentario} - <span style="font-weight: bold;">${c.cliente || 'Anônimo'}</span></p>
        `;
        comentariosLista.appendChild(comentarioEl);
      });
    }
  }


  // Abrir modal ao clicar em avaliações (AGORA USANDO DADOS JÁ ARMAZENADOS)
  const modal = document.getElementById("modal-avaliacoes");
  const fechar = document.getElementById("fechar-modal");

  document.querySelectorAll(".avaliacoes-prof").forEach(el => {
    el.addEventListener("click", async (e) => {
      const index = el.dataset.index;
      const profData = profissionais[index];

      const modalConteudo = modal.querySelector('.modal-conteudo');
      
      // Feedback de carregamento (apenas para o caso de um erro anterior)
      modalConteudo.innerHTML = `
        <span class="fechar-modal" id="fechar-modal-loading">&times;</span>
        <h4>Carregando Avaliações...</h4>
        <p style="text-align: center; margin-top: 20px; font-size: 1.2rem;">Aguarde. Carregando dados...</p>
      `; 
      document.getElementById("fechar-modal-loading").addEventListener("click", () => modal.style.display = "none");
      modal.style.display = "flex";

      // ⚠️ Tenta usar os dados já armazenados durante o fetchAndRenderProfStats.
      // Se não houver, faz o fetch de fallback (caso o usuário clique antes do fetch inicial terminar)
      let comentarios = profData.avaliacoes;

      if (!comentarios) {
           try {
                // Fetch de fallback: se os dados não foram carregados na inicialização
                const response = await fetch(`/backend/avaliacoes?id=${profData.id_db}`);
                if (!response.ok) throw new Error('Erro ao buscar avaliações no fallback.');
                comentarios = await response.json();
           } catch (error) {
                console.error("Erro no fetch de fallback:", error);
                modalConteudo.innerHTML = `<span class="fechar-modal" id="fechar-modal-api-erro">&times;</span><h4>Erro</h4><p style="color: red; text-align: center;">Não foi possível carregar os comentários.</p>`;
                document.getElementById("fechar-modal-api-erro").addEventListener("click", () => modal.style.display = "none");
                return;
           }
      }
      
      // Renderiza o modal com os dados
      renderizarModal(comentarios, profData.nome);

    });
  });

 
  // Lógica de fechar o modal
  fechar.addEventListener("click", () => modal.style.display = "none");
  window.addEventListener("click", e => {
    if (e.target === modal) modal.style.display = "none";
  });
});