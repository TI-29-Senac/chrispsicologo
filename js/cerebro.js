const track = document.getElementById('carrosselTrack');
const imagens = track.querySelectorAll('img');
const total = imagens.length;
const slideWidth = 500; // largura do slide

let index = 0;

// Clona os slides para criar efeito infinito suave
for (let i = 0; i < total; i++) {
  const clone = imagens[i].cloneNode(true);
  track.appendChild(clone);
}

function moverSlide() {
  index++;
  track.style.transition = 'transform 0.7s ease-in-out';
  track.style.transform = `translateX(${-index * slideWidth}px)`;

  // Quando chegar no final da cópia, volta para o início sem transição
  if (index === total) {
    setTimeout(() => {
      track.style.transition = 'none';
      track.style.transform = 'translateX(0)';
      index = 0;
    }, 3000); // tempo igual a duração da transição
  }
}

setInterval(moverSlide, 5000);
