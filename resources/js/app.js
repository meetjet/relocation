import './bootstrap';

import Alpine from 'alpinejs';
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm';
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm';

Alpine.plugin(FormsAlpinePlugin);
Alpine.plugin(NotificationsAlpinePlugin);

window.Alpine = Alpine;

Alpine.start();

let lastScrollTop = 0;
let categories = document.querySelector('#categories');

if (categories) {
    document.querySelector('body').classList.add('pt-28')
}

window.addEventListener('scroll', function () {
    if (!categories) {
        return true;
    }

    let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st > lastScrollTop && lastScrollTop > 20) { // downscroll code
        categories.classList.add('-mt-[40px]')
    } else { // upscroll code
        categories.classList.remove('-mt-[40px]')
    }

    lastScrollTop = st <= 0 ? 0 : st; // For Mobile or negative scrolling
})
