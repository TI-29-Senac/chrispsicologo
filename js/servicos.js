document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".serv-card");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("show");
      }
    });
  }, { threshold: 0.2 });

  cards.forEach(card => {
    card.classList.add("hidden-card");
    observer.observe(card);
  });
});

const items = document.querySelectorAll('.timeline-item.animate');

function revealOnScroll() {
  const triggerBottom = window.innerHeight * 0.85; // quando o topo do item chega a 85% da tela

  items.forEach(item => {
    const itemTop = item.getBoundingClientRect().top;

    if (itemTop < triggerBottom) {
      item.classList.add('show');
    }
  });
}

window.addEventListener('scroll', revealOnScroll);

// Dispara também no carregamento da página
window.addEventListener('load', revealOnScroll);
    