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

    // --- LÓGICA DE DUPLICAÇÃO MOVIDA PARA CÁ ---
    const originalCardsHTML = cardsContainerHome.innerHTML;
    // Duplica o conteúdo (vamos triplicar para ter blocos antes e depois)
    cardsContainerHome.innerHTML = originalCardsHTML + originalCardsHTML + originalCardsHTML;
    // --- FIM DA DUPLICAÇÃO ---

    const totalCards = cardsContainerHome.querySelectorAll('.card').length;
    let totalOriginalCards = 0;
    if (totalCards > 0) {
        // Calcula o número original de cards (total dividido por 3 agora)
        totalOriginalCards = totalCards / 3;

        // Verifica se totalOriginalCards é um número válido e maior que zero
        if (isNaN(totalOriginalCards) || totalOriginalCards <= 0) {
            console.error("Número original de cards inválido após duplicação.");
            return; // Evita erros de cálculo
        }

        // Posiciona o scroll no início do SEGUNDO bloco de cards
        // Usar Math.floor() para garantir que é um índice inteiro
        cardsContainerHome.scrollLeft = Math.floor(totalOriginalCards) * cardWidthHome;
    } else {
        console.warn("Nenhum card encontrado no carrossel após duplicação.");
        return; // Sai se não houver cards para evitar erros
    }

    destacarCardCentral(); // Destaca o card inicial
    setupCarrosselScroll(totalOriginalCards); // Passa o número original para a função de scroll
};

// Handler do evento de scroll MODIFICADO para receber totalOriginal
function handleCarrosselScroll(totalOriginal) {
    if (!cardsContainerHome || !totalOriginal || totalOriginal <= 0) return; // Validação adicional

    destacarCardCentral();

    // Lógica de scroll infinito (ajustada para usar totalOriginal)
    const umBloco = totalOriginal * cardWidthHome;
    const doisBlocos = 2 * umBloco;

    // Se está perto do início (primeiro bloco), pula para o segundo bloco
    // Ajuste a condição para ser mais robusta, talvez menor que meia largura de card
    if (cardsContainerHome.scrollLeft < cardWidthHome / 2) {
       console.log("Scroll perto do início, pulando para o meio...");
       cardsContainerHome.scrollLeft += umBloco;
    }
    // Se está perto do fim (terceiro bloco), volta para o segundo bloco
    // Ajuste a condição para ser mais robusta
    else if (cardsContainerHome.scrollLeft >= doisBlocos - (cardWidthHome / 2)) {
        console.log("Scroll perto do fim, voltando para o meio...");
        cardsContainerHome.scrollLeft -= umBloco;
    }
}

// Função para configurar o scroll MODIFICADA para passar totalOriginal
function setupCarrosselScroll(totalOriginal) {
     if (!cardsContainerHome) cardsContainerHome = document.getElementById("cards-carrossel");
     if (!cardsContainerHome) return;

     // Remove listener antigo para evitar duplicação
     cardsContainerHome.removeEventListener('scroll', cardsContainerHome._scrollHandler); // Usa uma referência guardada

     // Cria uma nova função handler que chama a original com o parâmetro
     cardsContainerHome._scrollHandler = () => handleCarrosselScroll(totalOriginal);

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

    document.querySelectorAll('.faixa-imagens img').forEach(img => {
        img.addEventListener('click', () => {
            if (lightbox && imgAmpliada) {
                imgAmpliada.src = img.src;
                lightbox.classList.remove('hidden');
            }
        });
    });

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