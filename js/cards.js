function toggleCard(selectedCard) {
    const allCards = document.querySelectorAll('.card');
    allCards.forEach(card => {
      if (card !== selectedCard) {
        card.classList.remove('active');
      }
    });
    selectedCard.classList.toggle('active');
  }