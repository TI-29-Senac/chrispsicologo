document.addEventListener("DOMContentLoaded", function() {
    // Definindo a estrutura HTML do modal como uma string literal
    const modalHTML = `
        <div id="login-modal" class="login-modal-overlay">
            <div class="login-modal-content">
                <span class="modal-close" onclick="fecharLoginModal()">&times;</span>
                <h2>Acesso ao Painel</h2>
                <form id="login-form" action="/backend/login" method="POST">
                    <label for="login-email">Email:</label>
                    <input type="email" id="login-email" name="email" required autocomplete="email">

                    <label for="login-senha">Senha:</label>
                    <input type="password" id="login-senha" name="senha" required autocomplete="current-password">

                    <button type="submit">Entrar</button>
                    <p id="login-status-message" style="margin-top: 10px; text-align: center; font-size: 0.9rem;"></p>
                </form>
                
                <div style="margin-top: 20px; text-align: center; font-size: 0.95rem;">
                    <a href="#" onclick="redirecionarRegistro(); return false;" style="color: #5D6D68; text-decoration: underline; display: block; margin-bottom: 8px;">Ainda não tem uma conta? Registre-se</a>
                    <a href="#" onclick="redirecionarEsqueciSenha(); return false;" style="color: #5D6D68; text-decoration: underline;">Esqueci minha senha</a>
                </div>
            </div>
        </div>
    `;

    // Injetar o modal no final do corpo do documento
    // 'beforeend' garante que ele fica logo antes do fechamento da tag </body>
    document.body.insertAdjacentHTML('beforeend', modalHTML);
});