/*
 * main.js - small interactions used on every page.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('is-open');
            navToggle.classList.toggle('is-open', isOpen);
            navToggle.setAttribute('aria-expanded', String(isOpen));
            navToggle.setAttribute('aria-label', isOpen ? 'Închide meniul' : 'Deschide meniul');
        });
    }

    // Close buttons on flash messages
    document.querySelectorAll('.alert-close').forEach((button) => {
        button.addEventListener('click', () => {
            const alert = button.closest('.alert');
            alert.classList.add('is-hiding');
            setTimeout(() => alert.remove(), 300);
        });
    });

    // Ask for confirmation before destructive actions (e.g. deleting a product)
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
});
