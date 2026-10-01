// Bootstrap's JS is imported per plugin so only what the site uses ships.
import 'bootstrap/js/dist/collapse';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);

/**
 * Export page: connects the world map's served countries to their market
 * cards. The map's paths and pins carry `data-iso` (baked in by
 * scripts/build-maps.mjs); each market card carries the same attribute from
 * its ExportMarket.iso_numeric. Hovering or focusing either side highlights
 * every element sharing that code — DOM lookup rather than a CSS rule per
 * country, since the country set is admin-editable, not fixed.
 */
Alpine.data('exportMarketMap', () => ({
    activeIso: null,

    highlight(iso) {
        if (!iso || iso === this.activeIso) {
            return;
        }

        this.clear();
        this.activeIso = iso;
        this.$root.querySelectorAll(`[data-iso="${iso}"]`).forEach((el) => {
            el.classList.add('radix-map__market--active');
        });
    },

    clear() {
        if (!this.activeIso) {
            return;
        }

        this.$root.querySelectorAll(`[data-iso="${this.activeIso}"]`).forEach((el) => {
            el.classList.remove('radix-map__market--active');
        });
        this.activeIso = null;
    },
}));

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

/**
 * Below-the-fold videos stay unloaded until they approach the viewport, so the
 * hero and first paint have the bandwidth to themselves on mobile. Users who
 * ask for reduced motion get the first frame and no playback.
 */
function initLazyVideo() {
    const videos = document.querySelectorAll('video[data-lazy-video]');

    if (!videos.length || !('IntersectionObserver' in window)) {
        return;
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const video = entry.target;
                observer.unobserve(video);

                video.querySelectorAll('source[data-src]').forEach((source) => {
                    source.src = source.dataset.src;
                });
                video.load();

                if (!reduceMotion) {
                    video.play().catch(() => {});
                }
            });
        },
        { rootMargin: '200px 0px' }
    );

    videos.forEach((video) => observer.observe(video));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLazyVideo);
} else {
    initLazyVideo();
}
