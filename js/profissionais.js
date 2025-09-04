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



  /* Página de profissionais */

document.addEventListener('DOMContentLoaded', () => {

  // Array de profissionais — no futuro, você vai buscar via fetch/axios do banco
  const profissionais = [
    {
      id: 1,
      nome: "Chris Nunes",
      profissao: "Psicólogo",
      especialidade: "Formado em Psicologia PCD e Inclusão Social",
      foto: "img/profissionais/chris.png",
      valor: "R$ 260,00",
      avaliacoes: [
        { estrelas: 5, comentario: "Ótima profissional, me ajudou muito a lidar com ansiedade." },
        { estrelas: 5, comentario: "Atendimento excelente, recomendo!" }
      ],
      duracao: "60min",
      tipo: ["Idosos", "Casais"]
    },
    {
      id: 2,
      nome: "Yan Nakamura",
      profissao: "Psicólogo",
      especialidade: "Formado em Psicoterapia Infantil e Adolescente",
      foto: "img/profissionais/pedro.png",
      valor: "R$ 260,00",
      avaliacoes: [
        { estrelas: 4, comentario: "Muito paciente." },
        { estrelas: 5, comentario: "Excelente profissional." }
      ],
      duracao: "60min",
      tipo: ["Adolescentes", "Casais"]
    }
    // Adicione outros profissionais aqui
  ];

  const container = document.getElementById('container-profissionais');

  profissionais.forEach(prof => {
    const card = document.createElement('div');
    card.classList.add('profissional');
    card.innerHTML = `
      <div class="card-prof">
        <div class="foto"><img src="${prof.foto}" alt="${prof.nome}"></div>
        <h3>${prof.nome}</h3>
        <div class="especialidades">${prof.profissao} - ${prof.especialidade}</div>
        <div class="valor">Valor da Consulta: ${prof.valor}</div>
        <a href="#" class="btn abrir-avaliacoes" data-prof="${prof.id}">Ver Avaliações</a>
      </div>
      <div class="detalhes">
        <h4>Duração e Tipo de Atendimento</h4>
        <div class="tipos">
          <ul>
            <li><img src="img/icons/relogio.svg" class="icon-prof"> Sessão com duração de ${prof.duracao}</li>
            ${prof.tipo.map(t => `<li><img src="img/icons/aneis.svg" class="icon-prof"> ${t}</li>`).join('')}
          </ul>
        </div>
      </div>
      <div class="modal-avaliacoes" data-prof="${prof.id}">
        <div class="modal-conteudo">
          <span class="fechar-modal" data-prof="${prof.id}">&times;</span>
          <h4>Comentários dos Clientes</h4>
          <div class="comentarios">
            ${prof.avaliacoes.map(av => `
              <div class="comentario">
                <div class="comentario-estrelas">
                  ${'★'.repeat(av.estrelas)}${'☆'.repeat(5 - av.estrelas)}
                </div>
                <p>${av.comentario}</p>
              </div>
            `).join('')}
          </div>
        </div>
      </div>
    `;
    container.appendChild(card);
  });

  // Função para abrir modal
  document.querySelectorAll('.abrir-avaliacoes').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const profId = btn.dataset.prof;
      const modal = document.querySelector(`.modal-avaliacoes[data-prof="${profId}"]`);
      modal.style.display = 'flex';
    });
  });

  // Fechar modal
  document.querySelectorAll('.fechar-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const profId = btn.dataset.prof;
      const modal = document.querySelector(`.modal-avaliacoes[data-prof="${profId}"]`);
      modal.style.display = 'none';
    });
  });

  // Fechar modal clicando fora
  document.querySelectorAll('.modal-avaliacoes').forEach(modal => {
    modal.addEventListener('click', e => {
      if (e.target === modal) modal.style.display = 'none';
    });
  });

});

