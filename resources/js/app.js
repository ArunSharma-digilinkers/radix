import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);

window.Alpine = Alpine;
Alpine.start();

/**
 * Scroll reveal.
 *
 * Sections marked [data-rev] fade and lift into view. Three rules matter here:
 *
 * 1. Nothing is hidden until JavaScript confirms IntersectionObserver works.
 *    Setting opacity:0 in CSS would leave the whole page invisible if the script
 *    fails to load — a blank page is a far worse outcome than no animation.
 * 2. Users who ask for reduced motion get no animation at all.
 * 3. Elements already in view on load are revealed immediately rather than
 *    animating, so the first screen never flashes.
 */
function initScrollReveal() {
    const sections = document.querySelectorAll('[data-rev]');

    if (!sections.length) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.dataset.rev = 'in';
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
    );

    sections.forEach((section) => {
        // Already on screen: show it without animating.
        if (section.getBoundingClientRect().top < window.innerHeight) {
            section.dataset.rev = 'in';

            return;
        }

        section.dataset.rev = 'out';
        observer.observe(section);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollReveal);
} else {
    initScrollReveal();
}
