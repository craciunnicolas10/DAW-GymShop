/*
 * carousel.js - image slideshow for the home page.
 * Features: previous/next buttons, dots, autoplay, pause on hover/focus,
 * keyboard arrows, swipe on touch screens and respect for "reduced motion".
 */
class Carousel {
    constructor(element) {
        this.element = element;
        this.slides = Array.from(element.querySelectorAll('.carousel__slide'));
        this.dots = Array.from(element.querySelectorAll('.carousel__dot'));
        this.interval = Number(element.dataset.interval) || 5000;
        this.currentIndex = 0;
        this.timer = null;
        this.touchStartX = 0;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        this.bindEvents();
        this.start();
    }

    bindEvents() {
        this.element.querySelector('.carousel__control--prev').addEventListener('click', () => this.restart(() => this.previous()));
        this.element.querySelector('.carousel__control--next').addEventListener('click', () => this.restart(() => this.next()));

        this.dots.forEach((dot) => {
            dot.addEventListener('click', () => this.restart(() => this.goTo(Number(dot.dataset.slide))));
        });

        this.element.addEventListener('mouseenter', () => this.stop());
        this.element.addEventListener('mouseleave', () => this.start());
        this.element.addEventListener('focusin', () => this.stop());
        this.element.addEventListener('focusout', () => this.start());

        this.element.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                this.previous();
            } else if (event.key === 'ArrowRight') {
                this.next();
            }
        });

        this.element.addEventListener('touchstart', (event) => {
            this.touchStartX = event.changedTouches[0].clientX;
        }, { passive: true });

        this.element.addEventListener('touchend', (event) => {
            const distance = event.changedTouches[0].clientX - this.touchStartX;
            if (Math.abs(distance) > 50) {
                this.restart(() => (distance > 0 ? this.previous() : this.next()));
            }
        });
    }

    goTo(index) {
        const total = this.slides.length;
        const newIndex = (index + total) % total;

        this.slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === newIndex;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
            slide.querySelectorAll('a, button').forEach((link) => {
                link.tabIndex = isActive ? 0 : -1;
            });
        });

        this.dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === newIndex);
            dot.setAttribute('aria-current', dotIndex === newIndex ? 'true' : 'false');
        });

        this.currentIndex = newIndex;
    }

    next() {
        this.goTo(this.currentIndex + 1);
    }

    previous() {
        this.goTo(this.currentIndex - 1);
    }

    start() {
        if (this.reducedMotion || this.timer !== null) {
            return;
        }
        this.timer = setInterval(() => this.next(), this.interval);
    }

    stop() {
        clearInterval(this.timer);
        this.timer = null;
    }

    restart(action) {
        this.stop();
        action();
        this.start();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-carousel]').forEach((element) => new Carousel(element));
});
