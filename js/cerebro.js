const track = document.getElementById('carrosselTrack');
const imagens = track.querySelectorAll('img');
const total = imagens.length;
let slideWidth = imagens[0].offsetWidth;
window.addEventListener('resize', () => {
  slideWidth = imagens[0].offsetWidth;
});


let index = 0;


for (let i = 0; i < total; i++) {
  const clone = imagens[i].cloneNode(true);
  track.appendChild(clone);
}

function moverSlide() {
  slideWidth = imagens[0].offsetWidth; // garante atualização
  index++;
  track.style.transition = 'transform 0.7s ease-in-out';
  track.style.transform = `translateX(${-index * slideWidth}px)`;


  if (index >= total) {
    setTimeout(() => {
      track.style.transition = 'none';
      track.style.transform = 'translateX(0px)';
      index = 0;
  
      // força reflow para garantir o reset
      void track.offsetWidth;
  
      // reativa a transição para o próximo movimento
      setTimeout(() => {
        track.style.transition = 'transform 0.7s ease-in-out';
      }, 20);
    }, 700);
  }
}  

setInterval(moverSlide, 5000);