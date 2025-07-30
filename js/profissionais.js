document.addEventListener('DOMContentLoaded', () => {
    const cardsContainer = document.querySelector('.cards');
    const cards = document.querySelectorAll('.card');
  
    function destacarCardCentral() {
      const containerRect = cardsContainer.getBoundingClientRect();
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
  
    destacarCardCentral();
  
    cardsContainer.addEventListener('scroll', () => {
      destacarCardCentral();
    });
  
    // Se tiver botões para scroll, garanta que eles funcionem:
    const leftBtn = document.querySelector('.seta.esquerda');
    const rightBtn = document.querySelector('.seta.direita');
    const scrollAmount = 300; // largura do card + gap (ajuste conforme seu layout)
  
    rightBtn.addEventListener('click', () => {
      cardsContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
  
    leftBtn.addEventListener('click', () => {
      cardsContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
  });
  