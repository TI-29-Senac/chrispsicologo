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
                <li><a href="minha-conta.html">Minha Conta</a></li>
                <li><a href="#" onclick="performLogout(event)">Sair</a></li>
            `;
        } else {
            sideMenuAuthItemHTML = '<li><a href="#" onclick="abrirLoginModal(); fecharMenuLateral(); return false;">Login</a></li>';
        }

        // --- Define o Ícone de Perfil e seu comportamento ---
        const profileIconSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
              <path fill-rulegit="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
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
                <a href="index.html"> <svg id="logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 375 375" class="logo">
                <defs> <style> .st0 { fill: #ddd; } </style> </defs> <path class="st0" d="M210.6,122.2v4.5c-1.4,0-4.1,0-5.1.9s-1.7,5.1-1.7,5.4v25.1c4.8-.5,10.6-1.5,13.1-6,5.9-10.5-7.9-32.4,15.3-29.2.4,2.7-3,1.7-4.1,3-2.2,2.5-.6,15.6-1.2,19.8-.9,6.7-7.7,12.2-13.8,13.9-3.3.9-8-.1-9,3s.4,7.9.8,9c1.5,3.6,7.6.6,6.3,6h-23.2v-3.7c8.4.9,6.8-6.7,6.6-12.6-8.6-.9-18-3.6-21.6-12.4s-.5-21.3-2.5-22.9-4.5-.3-4.2-2.9c20.6-2.5,13.1,12.1,14.2,24.4s6.8,9.8,14.3,10.8v-28.1c0-4.3-10.5-2.3-5.6-7.9h21.3ZM187.4,157.4c-18.5-4.1,3.5-32.9-17.2-33.7-.2,1.1,1.3.7,1.8,1.5,4.9,7-5.1,23.6,10.4,31.5,1,.5,4.4,2.6,5,.6ZM209.9,123.7h-20.2c0,2.8,3.3.7,5.2,3.4s1.5,5.3,1.6,7.4c.6,8.3.9,25.5-.1,33.6s-.6,3.5-1.4,4.6c-1.7,2.5-5.4,1.4-5.3,4.1h20.2c.4-2.5-3.1-1.3-4.8-3s-1.6-2.7-1.7-3.5c-1.2-6.1-.1-18.4-.1-25.3s-.9-3.5-.8-5.2.9-10.2,1.4-11.4,1-1.6,1.7-2c1.9-1.4,4.8-.2,4.5-2.6ZM228.6,123.7c-17.8.4-3.4,23.1-11.7,31.4s-3.8,1.4-4.8,3c8.7-1.4,13.4-8.4,14.2-16.8s-1.8-13.2,2.3-17.6Z"/> <path class="st0" d="M53.8,205.2v9.3c0,0,3,2,3,2l-9.7-.4,3.6-1.6c.5-1.4.3-18.9-.2-19.8s-3.1-.5-3.4-2.2h9.7c.7,2.7-2.5,1-3,1.5v9.7h13.5v-9.7c-.5-.5-3.7,1.2-3-1.5h9.7c-.3,1.7-2.7,1.2-3.4,2.2s-.7,18.4-.2,19.8l3.6,1.6-9.7.4,3-.7v-10.5h-13.5Z"/> <path class="st0" d="M87.8,192.5c3.6.6,4.9,4.2,3.8,7.5s-2.9,2.5-2.4,4.5,6.5,9.1,8.4,10.4,3.2.3,3.3,1.6c-2-.2-4.9.5-6.7-.4s-6.1-9.4-7.9-10.9-2.6-.8-4.1-.8c.2,2-.5,10.3.9,11.1s3-.7,2.9.8h-9.7s3-.7,3-.7v-21.7c0-.2-3,.2-3-1.5,3.5.4,8.3-.6,11.6,0ZM82.2,203c10.8,2.8,7-12.7.9-8.9s-.8,7.2-.9,8.9Z"/> <path class="st0" d="M276.6,192.7c19.6-4,21.1,21.7,5.6,24.3-17.4,3-20.3-21.4-5.6-24.3ZM274.1,213.2c.5.5,2.8,2,3.5,2.2,14.9,4.4,17.1-22.5.4-21.4s-8.9,14.1-3.8,19.2Z"/> <path class="st0" d="M228,192.7c19.5-3.6,21.2,21.7,4.9,24.3-16.8,2.7-19.7-21.6-4.9-24.3ZM227.2,194.2c-8.4,1.9-6.9,20.6,3.6,21.6,14.3,1.3,13-25.4-3.6-21.6Z"/> <path class="st0" d="M317.1,193.4l.9,5.8c-2.2-1.6-2.8-4.4-5.8-5.1-19.1-4.3-15.8,25.6,1.6,21.4l1.1-1.1v-6.3c-.4-.5-4.5,1.3-3.6-1.5h9s-2.3,1.9-2.3,1.9v7.8c-11.2,2.8-24.4-1.2-21.5-15.2s13.8-10.6,20.6-7.8Z"/> <path class="st0" d="M153.7,192.5c8.2,1.4,7.1,13.5-2.2,14.3-.8,0-2.9.5-2.6-.8,9.6,1,8.8-14.8-.3-11.9l-1.2,1v18.7c1.1,1.6,2.5,2.1,4.5,1.9v.7s-10.5,0-10.5,0v-.7s3,0,3,0v-21.7h-3s0-1.5,0-1.5c3.8.5,8.8-.6,12.4,0Z"/> <rect class="st0" x="29.1" y="239.7" width="93.6" height="1.5"/> <path class="st0" d="M360.7,215.7c.2,1.2-1,.7-1.9.7-2.4.1-4.8,0-7.1,0-.2-1.5,1.9-.4,2.2-.8,1.4-1.4-4.1-8.9-3.8-11.1-1.9,0-6.6-.8-7.8.7s-3.3,7.7-3.3,8.7c-.2,2.3,3.1.6,3.7,2.6h-9c.1-1.4,1.7-.4,2.9-1.9,2.1-2.5,8.7-22,10.2-22,4.9,6.1,6.5,16.5,11.6,22.1s1.5,1,2.3,1.1ZM348.7,203l-3-6-2.3,6h5.2Z"/> <path class="st0" d="M214.7,199.2c-1.3,0-1-1.9-1.9-2.9-3.4-3.8-11-3.2-14.2.7-6.2,7.6,1.9,22.6,11.6,18.4,3.3-1.5,2.7-3.7,5.2-5.6l-1.2,6.1c-28.4,9-27.8-31.3.1-21.8l.3,5.3Z"/> <path class="st0" d="M44.8,199.2l-5.2-4.9c-10.8-2.9-16.3,5.5-12.2,15.1s10.4,9.2,15.1,3.6,1-3.1,2.2-3.4c2.2,9.5-11.2,8.7-16.7,5.1-7.1-4.6-6.9-15.3,0-20,5.2-3.5,17.8-5,16.7,4.4Z"/> <path class="st0" d="M127.7,193.4l.9,5.1c-2.5.3-1.2-2.5-3-4.1-4.4-3.8-9.7,1.6-5.6,6.4,2.8,3.3,9.2,3.6,9.5,9.3.4,9.2-16.9,10.2-14.3-.4,2.8,3.8,4.4,8.1,10.2,5.3,5-7.8-6.4-9-8.6-13.3-4-7.8,4.7-11.4,11-8.3Z"/> <path class="st0" d="M175.6,193.4l.9,5.1c-2.5.3-1.2-2.5-3-4.1-4.4-3.8-9.7,1.6-5.6,6.4s9.2,3.6,9.5,9.3c.4,9.2-16.9,10.2-14.3-.4,2.8,3.8,4.4,8.1,10.2,5.3,5-7.8-6.4-9-8.6-13.3-4-7.8,4.7-11.4,11-8.3Z"/> <path class="st0" d="M249.9,194h-2.5s0-1.5,0-1.5h8.6c.5,2.4-1.6,1.3-3,1.5v21c2.6.9,8.1,1.4,10.1-.8s.2-4.2,2.6-3.7l-.7,6h-18l3-1.9v-20.6Z"/> <path class="st0" d="M108.4,215.7l3,.7-9.7-.4,2.9-.5c1.2-.8,1.4-20.1,0-21.4s-2.6,0-2.8-1.7h9.7c0,1.7-3,1.3-3,1.5v21.7Z"/> <path class="st0" d="M187,215.7l3,.7-9.7-.4,2.9-.5c1.2-.8,1.4-20.1,0-21.4s-2.6,0-2.8-1.7h9.7c0,1.7-3,1.3-3,1.5v21.7Z"/> <path class="st0" d="M329.2,214.6c.5,1.4,3.1.4,3,1.9l-9.7-.4,3.6-1.6c.5-1.4.3-18.9-.2-19.8s-3.1-.5-3.4-2.2h9.7c.5,2.4-1.6,1.3-3,1.5v20.6Z"/> <rect class="st0" x="258.9" y="240.4" width="93.6" height=".7"/> <path class="st0" d="M231.9,238.2c-3.3-.4-5.8-3.4-8.6,0-4.1,5.1,5,10.3,7.1,3.7h-4.1c-1.9-2.7,5.3-2.6,5.6-2.3v6.7c-2.3-1.4-3.9.6-5.5.6-11.6-.3-3.7-21.1,5.5-8.8Z"/> <path class="st0" d="M187,232.2l-1.5,2.2c8.2,0,8.1,12.9-.3,12.5-5-.3-7.8-7.4-4-10.8s2-.6,2.6-1.1c1.2-1.1,0-3.7,3.2-2.8ZM190,240.7c0-2.4-1.9-4.3-4.3-4.3s-4.3,1.9-4.3,4.3,1.9,4.3,4.3,4.3,4.3-1.9,4.3-4.3Z"/> <path class="st0" d="M217.8,240.6c0,3.4-2.8,6.2-6.2,6.2s-6.2-2.8-6.2-6.2,2.8-6.2,6.2-6.2,6.2,2.8,6.2,6.2ZM216,240.8c0-2.4-1.9-4.3-4.3-4.3s-4.3,1.9-4.3,4.3,1.9,4.3,4.3,4.3,4.3-1.9,4.3-4.3Z"/> <path class="st0" d="M157.1,238.2l-7.5-1.5.4,2.3c2.9,1.1,7.8,1.2,6.9,5.4-1.1,3.9-10.9,3.3-9.3-1.7,2.5,1.5,3.8,2.9,7.1,2.2v-2.9c0,0-6.8-2.1-6.8-2.1l-.6-2.7c2.2-4.4,8.1-3.6,9.6,1Z"/> <path class="st0" d="M247.2,240.6c0,3.4-2.8,6.2-6.2,6.2s-6.2-2.8-6.2-6.2,2.8-6.2,6.2-6.2,6.2,2.8,6.2,6.2ZM245.4,240.7c0-2.4-1.9-4.3-4.3-4.3s-4.3,1.9-4.3,4.3,1.9,4.3,4.3,4.3,4.3-1.9,4.3-4.3Z"/> <path class="st0" d="M134.6,235.5c10.3-5.5,15.3,8.5,2.2,7.1v3.8s-2.2,0-2.2,0v-10.9ZM136.9,241.2c7.6.5,8.5-5.7,0-4.5v4.5Z"/> <path class="st0" d="M176.5,238.9c-2.2.6-1.3-1.7-2.7-2.1-8.3-2.7-8.3,10.1-.8,8s1.8-2.5,3.5-2.1c.2,5-8.4,5.4-10.1,1.5-3.8-8.5,8.2-14.3,10.1-5.2Z"/> <path class="st0" d="M196.7,234.4v10.5c1.7.6,7.3-1.4,6.7,1.5h-8.2s0-12,0-12h1.5Z"/> <polygon class="st0" points="162.3 234.4 162.3 246.4 160.1 246.4 160.1 235.5 161.2 234.4 162.3 234.4"/> </svg>
                </a>

                <ul class="menu-navegacao">
                    ${desktopNavItemsHTML}
                </ul>

                <div class="header-actions"> 
                    ${profileIconHTML}
                    <button id="btn-more" aria-label="Abrir Menu">
                       <h3 class="mais-titulo">Menu</h3>
                        <div class="svg-wrapper">
                           <svg class="more" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Abrir menu">
  <circle cx="18" cy="12" r="1.5" fill="white"/>
  <circle cx="12" cy="12" r="1.5" fill="white"/>
  <circle cx="6" cy="12" r="1.5" fill="white"/>
</svg>

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