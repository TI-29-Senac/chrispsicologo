window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const logo = document.getElementById('logo');
    const menu = document.querySelectorAll('a');
    console.log(menu);
    if (window.scrollY > 0) {
        header.classList.add('header_menor');
        logo.classList.add('logo_menor');
        menu.forEach(item => {
            item.classList.add('menu_efeito');
        });
    } else {
        header.classList.remove('header_menor');
        logo.classList.remove('logo_menor');
        menu.forEach(item => {
            item.classList.remove('menu_efeito');
        });
    }
})