// js/carrossel.js

// ... (Variáveis globais e destacarCardCentral permanecem iguais) ...
let cardsContainerHome;
const scrollAmountHome = 300;
const cardWidthHome = 300 + 60; // Largura + gap

function destacarCardCentral() {
    // ... (código existente sem alterações) ...
    if (!cardsContainerHome) cardsContainerHome = document.getElementById("cards-carrossel");
    if (!cardsContainerHome) return;

    const cards = cardsContainerHome.querySelectorAll('.card');
    if (cards.length === 0) return;

    const containerRect = cardsContainerHome.getBoundingClientRect();
    const containerCenter = containerRect.left + containerRect.width / 2;

    let cardMaisProximo = null;
    let menorDistancia = Infinity;

    cards.forEach(card => {
        const cardRect = card.getBoundingClientRect();
        const cardCenter = cardRect.left + cardRect.width / 2;
        const distancia = Math.abs(containerCenter - cardCenter);

        if (distancia < menorDistancia) {
            menorDistancia = distancia;
            cardMaisProximo = card;
        }
    });

    cards.forEach(card => card.classList.remove('destaque'));
    if (cardMaisProximo) {
        cardMaisProximo.classList.add('destaque');
    }
}


// Função de inicialização MODIFICADA
window.inicializarCarrosselHome = () => {
    cardsContainerHome = document.getElementById("cards-carrossel");
    if (!cardsContainerHome) {
        console.error("Container do carrossel #cards-carrossel não encontrado ao inicializar.");
        return;
    }

    // REMOVIDO: Lógica de duplicação HTML (já feita no profissionais.js)
    
    const totalCards = cardsContainerHome.querySelectorAll('.card').length;
    
    if (totalCards > 0) {
        // Centraliza inicialmente (calcula metade da largura total de cards)
        const scrollCenter = (totalCards * cardWidthHome) / 2 - (cardsContainerHome.clientWidth / 2);
        cardsContainerHome.scrollLeft = Math.max(0, scrollCenter);
    }

    destacarCardCentral(); // Destaca o card inicial
    setupCarrosselScroll(); 
};

// Handler do evento de scroll SIMPLIFICADO
function handleCarrosselScroll() {
    if (!cardsContainerHome) return;
    destacarCardCentral();
}

// Função para configurar o scroll SIMPLIFICADA
function setupCarrosselScroll() {
     if (!cardsContainerHome) cardsContainerHome = document.getElementById("cards-carrossel");
     if (!cardsContainerHome) return;

     // Remove listener antigo para evitar duplicação
     if (cardsContainerHome._scrollHandler) {
        cardsContainerHome.removeEventListener('scroll', cardsContainerHome._scrollHandler);
     }

     // Cria uma nova função handler
     cardsContainerHome._scrollHandler = () => handleCarrosselScroll();

     // Adiciona o novo listener
     cardsContainerHome.addEventListener('scroll', cardsContainerHome._scrollHandler);
}


// Função global para os botões (mantida)
window.moverCarrossel = (direcao) => {
    // ... (código existente sem alterações) ...
    if (!cardsContainerHome) cardsContainerHome = document.getElementById("cards-carrossel");
    if (cardsContainerHome) {
        cardsContainerHome.scrollBy({ left: direcao * scrollAmountHome, behavior: 'smooth' });
    }
};

// Lógica do Lightbox (mantida como estava)
// ... (código existente do lightbox) ...
document.addEventListener('DOMContentLoaded', () => {
  const lightbox = document.getElementById('lightbox');
  const imgAmpliada = document.getElementById('img-ampliada');
  const faixaImagens = document.querySelector('.faixa-imagens');

  if (faixaImagens) {
    faixaImagens.addEventListener('click', (e) => {
      const img = e.target.closest('img'); 
      if (img && lightbox && imgAmpliada) {
        imgAmpliada.src = img.src;
        lightbox.classList.remove('hidden');
      }
    });
  }

  window.fecharLightbox = () => {
    if (lightbox && imgAmpliada) {
      lightbox.classList.add('hidden');
      imgAmpliada.src = '';
    }
  };

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && lightbox && !lightbox.classList.contains('hidden')) {
      fecharLightbox();
    }
  });

  if (lightbox) {
    lightbox.addEventListener('click', (e) => {
      if (e.target.id === 'lightbox') {
        fecharLightbox();
      }
    });
  }
});