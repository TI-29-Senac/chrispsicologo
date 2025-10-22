// js/loading.js
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loading-overlay');

    if (loadingOverlay) {
        // Adiciona a classe para iniciar o fade-out
        loadingOverlay.classList.add('hidden');

        // Opcional: Remover completamente o elemento após a transição
        // para liberar recursos (descomente se desejar)
        /*
        setTimeout(() => {
            if (loadingOverlay.parentNode) {
                 loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
        }, 500); // Tempo deve ser igual à duração da transição CSS (0.5s = 500ms)
        */
    }
});