// carrossel-conteudo.js

document.addEventListener('DOMContentLoaded', () => {
    const profissionais = [
      { "foto": "img/profissionais/chris.png", "nome": "Chris Nunes", "profissao": "Psicólogo", "especialidade": "Formado em Psicologia PCD e Inclusão Social" },
      { "foto": "img/profissionais/yan.png", "nome": "Yan Nakamura", "profissao": "Psicólogo", "especialidade": "Formado em Psicoterapia Infantil e Adolescente"},
      { "foto": "img/profissionais/luana.png", "nome": "Luana Duarte", "profissao": "Terapeuta", "especialidade": "Pós-graduada em Psicologia Clínica Existencial e Gestáltica"},
      { "foto": "img/profissionais/carla.png", "nome": "Carla Dias","profissao": "Psicóloga", "especialidade": "Pós-graduada em Neurociência e Psicologia Aplicada"},
      { "foto": "img/profissionais/helena.png", "nome": "Helena Costa", "profissao": "Terapeuta", "especialidade": "Formado em Terapia Cognitiva Comportamental"}
    ];

    const profissionaisDuplicados = [];
    var repeticoes = 5;
    for (let i = 0; i < repeticoes; i++) {
      profissionais.forEach((item) => {
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



 // aqui ja e da pagina dos profis fexo
document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("container-profissionais");


  const profissionais = [
  {
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
    avaliacoes: [
      { comentario: "Excelente profissional, muito atencioso.", nota: 5 },
      { comentario: "Me ajudou muito na sessão.", nota: 5 },
      { comentario: "Gostei do atendimento.", nota: 5 },
      { comentario: "Profissional competente e dedicado.", nota: 5 },
      { comentario: "Recomendo para todos.", nota: 5 }
    ]
  },
  {
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
    avaliacoes: [
      { comentario: "Excelente profissional, muito atenciosa.", nota: 5 },
      { comentario: "Ótima escuta e empatia.", nota: 4 },
      { comentario: "Senti progresso rápido nas sessões.", nota: 5 },
      { comentario: "Muito profissional e clara.", nota: 5 },
      { comentario: "Recomendo demais!", nota: 5 }
    ]
  },
  {
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
    avaliacoes: [
      { comentario: "Profissional excelente.", nota: 5 },
      { comentario: "Muito atenciosa e competente.", nota: 5 },
      { comentario: "Atendimento impecável.", nota: 5 },
      { comentario: "Ótima abordagem e técnicas.", nota: 4 },
      { comentario: "Senti melhorias significativas.", nota: 5 }
    ]
  },
  {
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
    avaliacoes: [
      { comentario: "Excelente trabalho com crianças.", nota: 5 },
      { comentario: "Atenciosa e empática.", nota: 4 },
      { comentario: "Recomendo para adolescentes.", nota: 5 },
      { comentario: "Muito profissional.", nota: 5 },
      { comentario: "Ajudou muito meu filho.", nota: 5 }
    ]
  },
  {
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
    avaliacoes: [
      { comentario: "Muito atencioso e competente.", nota: 5 },
      { comentario: "Ajudou bastante nas sessões.", nota: 4 },
      { comentario: "Profissional de confiança.", nota: 5 },
      { comentario: "Ótima escuta e técnicas.", nota: 5 },
      { comentario: "Recomendo sem dúvidas.", nota: 5 }
    ]
  }
];


  profissionais.forEach((prof, index) => {
    const card = document.createElement("div");
    card.classList.add("profissional");
    nota_media = prof.avaliacoes.reduce((acc, curr) => acc + curr.nota, 0) / prof.avaliacoes.length;
    qtde_avaliacoes = prof.avaliacoes.length;
    card.innerHTML = `
      <div class="card-prof">
        <h3>${prof.nome}</h3>
        <div class="foto"><img src="${prof.foto}" alt="${prof.nome}"></div>

        <div class="avaliacoes-prof" data-index="${index}">
          <div class="estrelas-media-container">
            <span class="total-avaliacoes">${qtde_avaliacoes} Avaliações</span>
            <div class="estrelas-media">
              ${"★".repeat(Math.round(nota_media))}${"☆".repeat(5-Math.round(nota_media))}
            </div>
            <span class="nota-media">${nota_media.toFixed(1)}</span>
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



  // Modal de avaliações
  const modal = document.getElementById("modal-avaliacoes");
  const fechar = document.getElementById("fechar-modal");
  const comentariosContainer = document.getElementById("comentarios-container");

  // Abrir modal ao clicar em avaliações
  document.querySelectorAll(".avaliacoes-prof").forEach(el => {
    el.addEventListener("click", e => {
      const index = el.dataset.index;
      const comentarios = profissionais[index].avaliacoes;



    const avaliacoes_template = document.getElementById("avaliacoes-template");

    // Conta quantidade por nota (1–5)
const contagem = {1:0,2:0,3:0,4:0,5:0};
comentarios.forEach(c => {
  const n = Math.round(c.nota);
  contagem[n]++;
});

const total = comentarios.length;

// Limpa o container antes de preencher
comentariosContainer.innerHTML = "";

// Cria linhas de 5 até 1
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

  comentariosContainer.appendChild(linha);
}

// Depois adiciona os comentários
comentarios.forEach(c => {
  const comentarioEl = document.createElement("div");
  comentarioEl.className = "comentario";
  comentarioEl.innerHTML = `
    <div class="comentario-estrelas">
      ${"★".repeat(Math.round(c.nota))}${"☆".repeat(5 - Math.round(c.nota))}
    </div>
    <p>${c.comentario}</p>
  `;
  comentariosContainer.appendChild(comentarioEl);
});

modal.style.display = "flex";

      // Preenche os comentários



      modal.style.display = "flex";
    });
  });

 
  fechar.addEventListener("click", () => modal.style.display = "none");
  window.addEventListener("click", e => {
    if (e.target === modal) modal.style.display = "none";
  });
});


