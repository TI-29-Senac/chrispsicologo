// carrossel-funcional.js

document.addEventListener('DOMContentLoaded', () => {
  const cardsContainer = document.getElementById("cards-carrossel");
  const scrollAmount = 300;
  const cardWidth = 300 + 60; // largura do card + gap

  const totalCards = cardsContainer.querySelectorAll('.card').length;
  const middleIndex = Math.floor(totalCards / 3); // onde começa o segundo bloco

  // Começa no centro
  cardsContainer.scrollLeft = middleIndex * cardWidth;

  // Destaca o card mais central
  function destacarCardCentral() {
    const cards = cardsContainer.querySelectorAll('.card');
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

  // Scroll infinito
  cardsContainer.addEventListener('scroll', () => {
    destacarCardCentral();

    const totalOriginal = totalCards / 3;

    if (cardsContainer.scrollLeft <= cardWidth) {
      cardsContainer.scrollLeft += totalOriginal * cardWidth;
    }

    if (cardsContainer.scrollLeft >= (totalOriginal * 2) * cardWidth) {
      cardsContainer.scrollLeft -= totalOriginal * cardWidth;
    }
  });

  // Botões ← e →
  window.moverCarrossel = (direcao) => {
    cardsContainer.scrollBy({ left: direcao * scrollAmount, behavior: 'smooth' });
  };

  destacarCardCentral(); 
});
