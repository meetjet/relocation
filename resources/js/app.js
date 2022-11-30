import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

let lastScrollTop = 0;
let categories = document.querySelector('#categories');

window.addEventListener('scroll', function () {
    let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st > lastScrollTop && lastScrollTop > 20) { // downscroll code
        categories.classList.add('-mt-[40px]')
    } else { // upscroll code
        categories.classList.remove('-mt-[40px]')
    }

    lastScrollTop = st <= 0 ? 0 : st; // For Mobile or negative scrolling
})
