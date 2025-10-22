  

function toggleCard(selectedCard) {
  const allCards = document.querySelectorAll('.card-sentimento');
  allCards.forEach(card => {
    if (card !== selectedCard) {
      card.classList.remove('active');
    }
  });
  selectedCard.classList.toggle('active');
}



  

document.querySelectorAll('.card-servico img').forEach(img => {
  img.style.cursor = 'pointer'; 

  img.addEventListener('click', () => {
    
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = 0;
    modal.style.left = 0;
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.style.zIndex = 9999;
    modal.style.cursor = 'zoom-out';

    
    const modalImg = document.createElement('img');
    modalImg.src = img.src;
    modalImg.alt = img.alt;
    modalImg.style.maxWidth = '90%';
    modalImg.style.maxHeight = '90%';
    modalImg.style.borderRadius = '12px';
    modalImg.style.boxShadow = '0 0 30px rgba(0,0,0,0.7)';

    modal.appendChild(modalImg);

    
    modal.addEventListener('click', () => {
      modal.remove();
    });

    
    document.body.appendChild(modal);
  });
});
