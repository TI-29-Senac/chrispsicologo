window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const logo = document.getElementById('logo');
    const menu = document.querySelectorAll('a');
  
    if (!logo) {
      console.error('Logo não encontrada!');
      return;
    }
  
    if (window.scrollY > 0) {
      header.classList.add('header_menor');
      logo.classList.add('logo_menor');
      menu.forEach(item => item.classList.add('menu_efeito'));
    } else {
      header.classList.remove('header_menor');
      logo.classList.remove('logo_menor');
      menu.forEach(item => item.classList.remove('menu_efeito'));
    }
  
    console.log(logo.classList);
  });

 

const botaoMais = document.getElementById('btn-more');
const menuLateral = document.getElementById('menu-lateral');
const botaoFechar = document.getElementById('botao-fechar');
const overlay = document.getElementById('fundo-escuro');

if (botaoMais && menuLateral && botaoFechar && overlay) {
  botaoMais.addEventListener('click', () => {
    menuLateral.classList.add('aberto');
    overlay.classList.add('ativo');
  });

  botaoFechar.addEventListener('click', () => {
    menuLateral.classList.remove('aberto');
    overlay.classList.remove('ativo');
  });

  overlay.addEventListener('click', () => {
    menuLateral.classList.remove('aberto');
    overlay.classList.remove('ativo');
  });
} else {
  console.warn('Algum dos elementos do menu lateral não foi encontrado no DOM.');
}
