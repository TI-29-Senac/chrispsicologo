// chrispsicologo/js/header.js

// Função para aplicar/remover classes de header menor no scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const logo = document.getElementById('logo');

    if (!header || !logo) {
      return;
    }

    if (window.scrollY > 0) {
      header.classList.add('header_menor');
      logo.classList.add('logo_menor');
    } else {
      header.classList.remove('header_menor');
      logo.classList.remove('logo_menor');
    }
});

// Função global para logout
window.performLogout = function(event) {
    event.preventDefault();
    sessionStorage.removeItem('welcomeUserName');
    window.location.href = '/backend/logout';
}

// Função para ativar os listeners do menu lateral
function ativarMenuLateral() {
    const btnMore = document.getElementById("btn-more");
    const menuLateral = document.getElementById("menu-lateral");
    const botaoFechar = document.getElementById("botao-fechar");
    const overlay = document.getElementById("fundo-escuro");
    const profileIconBtn = document.getElementById("profile-icon-btn"); // Pegar o botão do ícone de perfil

    // Função para abrir o menu lateral
    const abrirMenuLateral = () => {
         if (menuLateral && overlay) {
            menuLateral.classList.add("aberto");
            overlay.classList.add("ativo");
         }
    };

    // Função para fechar o menu lateral
    const fecharMenuLateral = () => {
         if (menuLateral && overlay) {
            menuLateral.classList.remove("aberto");
            overlay.classList.remove("ativo");
         }
    };


    if (btnMore) {
        btnMore.addEventListener("click", abrirMenuLateral);
    }

    if (botaoFechar) {
        botaoFechar.addEventListener("click", fecharMenuLateral);
    }

    if (overlay) {
        overlay.addEventListener("click", fecharMenuLateral);
    }

    // Adiciona listener ao botão de ícone de perfil (se existir e se usuário estiver logado)
    if (profileIconBtn && sessionStorage.getItem('welcomeUserName')) {
         profileIconBtn.addEventListener("click", (e) => {
             e.preventDefault();
             abrirMenuLateral();
         });
    } else if (profileIconBtn) {
         // Se não estiver logado, o onclick="abrirLoginModal()" já está no HTML
    }

}


document.addEventListener("DOMContentLoaded", () => {

    const navElement = document.getElementById("navbar");
    const userName = sessionStorage.getItem('welcomeUserName');

    if (navElement) {

        // --- Define os itens principais da navegação desktop ---
        let desktopNavItemsHTML = '';
        const currentPath = window.location.pathname;
        // (Lógica para desktopNavItemsHTML permanece a mesma)
        if (currentPath.includes("contato.html")) {
            desktopNavItemsHTML = `
                <li><a href="index.html">Início</a></li>
                <li><a href="servicos.html">Serviços</a></li>
                <li><a href="sobre.html">Sobre</a></li>
            `;
        } else if (currentPath.includes("servicos.html")) {
            desktopNavItemsHTML = `
                <li><a href="index.html">Início</a></li>
                <li><a href="contato.html">Contato</a></li>
                <li><a href="sobre.html">Sobre</a></li>
            `;
        } else if (currentPath.includes("sobre.html")) {
            desktopNavItemsHTML = `
                <li><a href="index.html">Início</a></li>
                <li><a href="servicos.html">Serviços</a></li>
                <li><a href="contato.html">Contato</a></li>
            `;
        } else { // index.html ou outras
            desktopNavItemsHTML = `
                <li><a href="servicos.html">Serviços</a></li>
                <li><a href="contato.html">Contato</a></li>
                <li><a href="sobre.html">Sobre</a></li>
            `;
        }

        // --- Define o item de autenticação para o menu lateral ---
        let sideMenuAuthItemHTML = '';
        if (userName) {
             sideMenuAuthItemHTML = `
                <li class="welcome-user-side"><span>Olá, ${userName}!</span></li>
                <li><a href="#">Minha Conta</a></li>
                <li><a href="#" onclick="performLogout(event)">Sair</a></li>
            `;
        } else {
            sideMenuAuthItemHTML = '<li><a href="#" onclick="abrirLoginModal(); fecharMenuLateral(); return false;">Login</a></li>';
        }

        // --- Define o Ícone de Perfil e seu comportamento ---
        const profileIconSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" width="54" height="54" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
            </svg>
        `;

        let profileIconHTML = '';
        if (userName) {
            // Logado: Ícone abre o menu lateral (agora via event listener)
            profileIconHTML = `
                <button id="profile-icon-btn" class="profile-icon-btn" aria-label="Menu do Usuário">
                    ${profileIconSVG}
                </button>
            `;
        } else {
            // Não logado: Ícone abre o modal de login
            profileIconHTML = `
                <button id="profile-icon-btn" class="profile-icon-btn" aria-label="Fazer Login" onclick="abrirLoginModal(); return false;">
                    ${profileIconSVG}
                </button>
            `;
        }

        // --- Gera o HTML completo do Header e Menu Lateral ---
        navElement.innerHTML = `
           <header>
            <nav class="menu">
                <div class="container">
                <a href="index.html"><img src="img/logo/logochris.svg" alt="Logo Chris Psicologia" class="logo" id="logo"></a>

                <ul class="menu-navegacao">
                    ${desktopNavItemsHTML}
                </ul>

                <div class="header-actions"> 
                    ${profileIconHTML}
                    <button id="btn-more" aria-label="Abrir Menu">
                       <h3 class="mais-titulo">Menu</h3>
                        <div class="svg-wrapper">
                           <img src="img/icons/mais.svg" alt="Abrir menu" class="more">
                       </div>
                   </button>
               </div>

                </div>
            </nav>
            </header>
             <nav id="menu-lateral" class="menu-lateral">
                <button id="botao-fechar" class="botao-fechar" aria-label="Fechar menu">×</button>

                <ul class="links-menu-lateral">
                    <li><a href="index.html">Início</a></li>
                    <li><a href="servicos.html">Serviços</a></li>
                    <li><a href="profissionais.html">Agendar</a></li>
                    <li><a href="contato.html">Contato</a></li>
                    <li><a href="sobre.html">Sobre</a></li>
                    <li><a href="termos.html">Termos de Uso</a></li>
                    <li><a href="politicas.html">Politicas de Privacidade</a></li>
                    ${sideMenuAuthItemHTML}
                </ul>

                <div class="redes-menu-lateral">
                  
                     <a href="https://instagram.com" target="_blank" aria-label="Instagram" class="icone-instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="currentColor"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5h-8.5zm8.5 2.75a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5zM12 7a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z" /></svg>
                    </a>
                    <a href="https://wa.me/5511999999999" target="_blank" aria-label="WhatsApp" class="icone-whatsapp"> <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="currentColor"/></svg>
                    </a>
                    <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn" class="icone-linkedin">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5a2.5 2.5 0 11-.001 5.001A2.5 2.5 0 014.98 3.5zM3 9h4v12H3zM8.5 9h3.5v1.69a3.95 3.95 0 013.5-1.88c3.75 0 4.5 2.47 4.5 5.68V21h-4v-5.19c0-1.23-.02-2.82-1.72-2.82-1.72 0-1.98 1.34-1.98 2.73V21H8.5z" /></svg>
                    </a>
                </div>
            </nav>
            <div id="fundo-escuro" class="overlay"></div>
        `;

        // Ativa os listeners do menu lateral DEPOIS que o HTML foi injetado
        ativarMenuLateral();

    } else {
        console.error("Elemento #navbar não encontrado no DOM.");
    }

}); // Fim do DOMContentLoaded