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

document.querySelectorAll('.abrir-avaliacoes').forEach(btn => {
  btn.addEventListener('click', () => {
    const profId = btn.dataset.prof;
    const modal = document.querySelector(`.modal-avaliacoes[data-prof="${profId}"]`);
    
    // Aqui você pode carregar do banco via fetch/ajax
    // Ex: carregarComentarios(profId).then(...)

    modal.style.display = 'block';
  });
});

document.querySelectorAll('.fechar-modal').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal-avaliacoes').style.display = 'none';
  });
});
