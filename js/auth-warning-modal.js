document.addEventListener("DOMContentLoaded", function () {
    // Defines the HTML structure of the warning modal
    // Reuses classes from login-modal for consistent styling
    const modalHTML = `
        <div id="auth-warning-modal" class="login-modal-overlay" style="z-index: 2000;">
            <div class="login-modal-content" style="max-width: 400px; text-align: center;">
                <span class="modal-close" onclick="fecharAuthWarningModal()">&times;</span>
                <h2 style="margin-bottom: 15px; color: #5D6D68;">Login Necessário</h2>
                <p style="margin-bottom: 25px; color: #666; font-size: 1.1rem;">
                    Para agendar uma consulta, você precisa estar logado ou criar uma conta.
                </p>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button onclick="abrirLoginDoWarning()" class="consulta-botao" style="width: 100%; border-radius: 8px; font-size: 1rem; padding: 12px;">Fazer Login / Criar Conta</button>
                    <button onclick="fecharAuthWarningModal()" style="background: none; border: none; color: #999; cursor: pointer; text-decoration: underline; font-size: 0.9rem;">Cancelar</button>
                </div>
            </div>
        </div>
    `;

    // Inject the modal before the body ends
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Global functions to control the modal
    window.abrirAuthWarningModal = () => {
        const modal = document.getElementById('auth-warning-modal');
        if (modal) {
            modal.classList.add('open'); // Assumes 'open' class controls visibility like login modal
            // Fallback if 'open' class isn't enough (e.g., if style.css uses different mechanism)
            modal.style.display = 'flex';
        }
    };

    window.fecharAuthWarningModal = () => {
        const modal = document.getElementById('auth-warning-modal');
        if (modal) {
            modal.classList.remove('open');
            modal.style.display = 'none';
        }
    };

    window.abrirLoginDoWarning = () => {
        fecharAuthWarningModal();
        if (typeof window.abrirLoginModal === 'function') {
            window.abrirLoginModal();
        } else {
            console.error("Função abrirLoginModal não encontrada. Redirecionando para login...");
            window.location.href = '/minha-conta.html'; // Fallback
        }
    };

    // Close on click outside
    const modal = document.getElementById('auth-warning-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                fecharAuthWarningModal();
            }
        });
    }

    // Ensure it's hidden initially
    if (modal) modal.style.display = 'none';
});
