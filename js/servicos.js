document.addEventListener("DOMContentLoaded", () => {
    
    // --- Lógica de Animação (IntersectionObserver) ---
    // Mantemos o observer definido no escopo
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.2 });

    
    // --- Lógica para buscar e renderizar os serviços (NOVA) ---
    const servicosGrid = document.getElementById('servicos-grid-container');
    
    if (servicosGrid) {
        
        fetch('/backend/api/servicos/listar') // Chama a nova API
            .then(response => {
                if (!response.ok) {
                    throw new Error('Falha ao carregar serviços. Status: ' + response.status);
                }
                return response.json();
            })
            .then(json => {
                if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                    servicosGrid.innerHTML = ''; // Limpa a mensagem de "Carregando..."
                    
                    json.data.forEach(servico => {
                        // O HTML do card é baseado no seu HTML estático
                        const cardHtml = `
                            <div class="serv-card hidden-card">
                                <div class="serv-icon">
                                    <img src="${servico.icone_path}" alt="${servico.titulo}">
                                </div>
                                <h3 class="serv-card-title">${servico.titulo}</h3>
                                <p class="serv-card-text">
                                    ${servico.descricao}
                                </p>
                            </div>
                        `;
                        servicosGrid.insertAdjacentHTML('beforeend', cardHtml);
                    });
                    
                    // **CRÍTICO**: Aplica o observer aos novos cards que acabaram de ser criados
                    const novosCards = servicosGrid.querySelectorAll(".serv-card");
                    novosCards.forEach(card => {
                        // card.classList.add("hidden-card"); // A classe já foi adicionada no HTML
                        observer.observe(card);
                    });
                    
                } else {
                    servicosGrid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; color: #5D6D68;">Nenhum serviço ativo encontrado.</p>';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar serviços:', error);
                servicosGrid.innerHTML = `<p style="grid-column: 1 / -1; text-align: center; color: red;">Erro ao carregar serviços: ${error.message}</p>`;
            });
    }
    // --- Fim da lógica de busca ---


    // --- Lógica de Animação da Timeline (Original) ---
    // Esta parte do seu arquivo original é mantida
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
    window.addEventListener('load', revealOnScroll);
});