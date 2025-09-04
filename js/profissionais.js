// carrossel-conteudo.js

document.addEventListener('DOMContentLoaded', () => {
    const profissionais = [
      { "foto": "img/profissionais/chris.png", "nome": "Chris Nunes", "profissao": "Psicólogo", "especialidade": "Formado em Psicologia PCD e Inclusão Social" },
      { "foto": "img/profissionais/pedro.png", "nome": "Yan Nakamura", "profissao": "Psicólogo", "especialidade": "Formado em Psicoterapia Infantil e Adolescente"},
      { "foto": "img/profissionais/luana.png", "nome": "Luana Duarte", "profissao": "Terapeuta", "especialidade": "Pós-graduada em Psicologia Clínica Existencial e Gestáltica"},
      { "foto": "img/profissionais/larissa.png", "nome": "Carla Dias","profissao": "Psicóloga", "especialidade": "Pós-graduada em Neurociência e Psicologia Aplicada"},
      { "foto": "img/profissionais/helena.png", "nome": "Helena Costa", "profissao": "Terapeuta", "especialidade": "Formado em Terapia Cognitiva Comportamental"}
    ];
  
    const cardsContainer = document.getElementById("cards-carrossel");
  
    
    const totalReps = 3;
    for (let i = 0; i < totalReps; i++) {
      profissionais.forEach((item) => {
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




document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("container-profissionais");

 const profissionais = [
  {
    nome: "Chris Nunes",
    foto: "/img/profissionais/chris.png",
    valor: "260,00",
    especialidades: ["Psicoterapia Individual",
        "Relacionamentos",
         "Psicologia do Envelhecimento",
          "Ansiedade e Depressão em Adultos",
           "Luto e Perdas",
            "Conflitos Profissionais e Familiares",
              "Comunicação e Mediação de Conflitos"],
    duracao: "Sessão com duração de 60min",
    tipos: [
      { icone: "/img/icons/old-man.svg", texto: "Idosos" },
      { icone: "/img/icons/casais.svg", texto: "Casais" },
      { icone: "/img/icons/adulto.svg", texto: "Adultos" }
    ],
    avaliacoes: {
      nota: 5.0,
      total: 29,
      comentarios: [
        "Ótima profissional, me ajudou muito a lidar com ansiedade.",
        "Atendimento excelente, recomendo!"
      ]
    }
  },
  {
    nome: "Helena Costa",
    foto: "/img/profissionais/helena.png",
    valor: "280,00",
    especialidades: ["Psicoterapia para Mulheres",
"Ansiedade, Depressão e Estresse em Mulheres",
"Autoestima e Autoconhecimento",
"Relacionamentos Afetivos e Abusivos",
"Luto, Perdas e Ciclos de Vida",
"Psicologia da Maternidade e Pós-Parto",
"Apoio Psicológico na Gravidez e Planejamento Familiar"],
    duracao: "Sessão com duração de 50min",
    tipos: [
      { icone: "/img/icons/mulheres.svg", texto: "Mulheres"},
      { icone: "/img/icons/publico_feminino.svg", texto: "Abordagens Femininas" },
      { icone: "/img/icons/maternidade.svg", texto: "Maternidade" }
    ],
    avaliacoes: {
      nota: 5.0,
      total: 15,
      comentarios: [
        "Profissional excelente, muito atenciosa!",
        "Gostei bastante das sessões, recomendo."
      ]
    }
  },
  {
    nome: "Carla Dias",
    foto: "/img/profissionais/carla.png",
    valor: "240,00",
    especialidades: ["Psicoterapia Individual",
"Terapia de Casal e Família",
"Ansiedade e Depressão em Adultos",
"Psicologia do Envelhecimento",
"Estresse Ocupacional e Burnout",
"Luto e Processos de Perda",
"Desenvolvimento de Habilidades Sociais"],
    duracao: "Sessão com duração de 45min",
    tipos: [
      { icone: "/img/icons/adulto.svg", texto: "Adultos"},
      { icone: "/img/icons/old-man.svg", texto: "Idosos"},
      { icone: "/img/icons/casais.svg", texto: "Casais"}
    ],
    avaliacoes: {
      nota: 5.0,
      total: 10,
      comentarios: [
        "Minha filha melhorou muito depois das sessões.",
        "Profissional dedicada, ótimo acompanhamento."
      ]
    }
  },
  {
    nome: "Luana Duarte",
    foto: "/img/profissionais/luana.png",
    valor: "300,00",
    especialidades: ["Psicoterapia Infantil",
"Orientação Parental",
"Transtornos de Aprendizagem e Desenvolvimento",
"Ansiedade e Autoestima em Crianças e Adolescentes",
"Conflitos Escolares e Bullying",
"Orientação em Relacionamentos e Sexualidade na Adolescência",
"Luto e Adaptação a Mudanças"],
    duracao: "Sessão com duração de 55min",
    tipos: [
      { icone: "/img/icons/crianca.svg", texto: "Crianças"},
      { icone: "/img/icons/adolescente.svg", texto: "Adolescentes"}
    ],
    avaliacoes: {
      nota: 5.0,
      total: 21,
      comentarios: [
        "Uma excelente psicóloga, mudou minha vida.",
        "Muito empática e atenciosa."
      ]
    }
  },
  {
    nome: "Yan Nakamura",
    foto: "/img/profissionais/yan.png",
    valor: "270,00",
    especialidades: ["Psicoterapia para Jovens Adultos e Adolescentes",
"Orientação Vocacional e Profissional",
"Ansiedade, Autoestima e Identidade",
"Relacionamentos Interpessoais",
"Luto e Mudanças de Ciclo de Vida",
"Sexualidade e Questões de Gênero",
"Psicoterapia Familiar e Mediação de Conflitos"],
    duracao: "Sessão com duração de 60min",
    tipos: [
      { icone: "/img/icons/adulto.svg", texto: "Jovens Adultos" },
      { icone: "/img/icons/adolescente.svg", texto: "Adolescentes"},
      { icone: "/img/icons/familia.svg", texto: "Família"}
    ],
    avaliacoes: {
      nota: 5.0,
      total: 18,
      comentarios: [
        "Me ajudou muito a lidar com o estresse do trabalho.",
        "Sessões muito produtivas e diretas."
      ]
    }
  }
];

  profissionais.forEach((prof, index) => {
    const card = document.createElement("div");
    card.classList.add("profissional");
    card.innerHTML = `
      <div class="card-prof">
        <h3>${prof.nome}</h3>
        <div class="foto"><img src="${prof.foto}" alt="${prof.nome}"></div>

        <div class="avaliacoes-prof" data-index="${index}">
          <div class="estrelas-media-container">
            <span class="total-avaliacoes">${prof.avaliacoes.total} Avaliações</span>
            <div class="estrelas-media">
              ${"★".repeat(Math.round(prof.avaliacoes.nota))}
            </div>
            <span class="nota-media">${prof.avaliacoes.nota.toFixed(1)}</span>
          </div>
        </div>

        <div class="valor">Valor da Consulta: R$${prof.valor}</div> 
        <a href="#" class="btn">Agenda Disponível</a>
      </div>

      <div class="detalhes">
        <h4>Especialidades</h4>
        <ul class="especialidades-lista">
          ${prof.especialidades.map(e => `<li>${e}</li>`).join("")}
        </ul>

        <h4 class="espaco-prof">Duração e Tipo de Atendimento</h4>
        <div class="tipos">
          <ul>
            <li><img src="/img/icons/relogio.svg" class="icon-prof"> ${prof.duracao}</li>
            ${prof.tipos.map(t => `
              <li><img src="${t.icone}" class="icon-prof"> ${t.texto}</li>
            `).join("")}
          </ul>
        </div>
      </div>
    `;
    container.appendChild(card);
  });

  // Modal de avaliações
  const modal = document.getElementById("modal-avaliacoes");
  const fechar = document.getElementById("fechar-modal");
  const comentariosContainer = document.getElementById("comentarios-container");

  // Abrir modal ao clicar em avaliações
  document.querySelectorAll(".avaliacoes-prof").forEach(el => {
    el.addEventListener("click", e => {
      const index = el.dataset.index;
      const comentarios = profissionais[index].avaliacoes.comentarios;

      // Preenche os comentários
      comentariosContainer.innerHTML = comentarios.map(c => `
        <div class="comentario">
          <div class="comentario-estrelas">
            ${"★".repeat(5)}
          </div>
          <p>${c}</p>
        </div>
      `).join("");

      modal.style.display = "flex";
    });
  });

 
  fechar.addEventListener("click", () => modal.style.display = "none");
  window.addEventListener("click", e => {
    if (e.target === modal) modal.style.display = "none";
  });
});


