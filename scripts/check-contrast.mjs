/**
 * Checks the design tokens in resources/css/app.css against WCAG 2.1 AA.
 *
 * The brief requires "proper contrast ratios" (§7), and accessibility is easiest
 * to keep once it is mechanical. This reads the tokens straight out of app.css so
 * it cannot drift from what actually ships.
 *
 * Run with `npm run check:contrast`. Exits non-zero on a failure.
 *
 * Thresholds (WCAG 2.1):
 *   normal text  4.5:1   — body copy, captions, labels, placeholders
 *   large text   3.0:1   — >=24px, or >=18.66px when bold
 *   ui           3.0:1   — borders and other non-text boundaries
 */
import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const CSS = resolve(ROOT, 'resources/css/app.css');

const THRESHOLDS = { normal: 4.5, large: 3, ui: 3 };

/** Foreground / background pairs as they are actually combined in the UI. */
const PAIRS = [
    // On white
    ['ink', 'white', 'normal', 'body copy'],
    ['ink-soft', 'white', 'normal', 'pull-quote body'],
    ['nav', 'white', 'normal', 'header nav links'],
    ['lead', 'white', 'normal', 'lead paragraphs'],
    ['muted', 'white', 'normal', 'secondary body'],
    ['meta', 'white', 'normal', 'captions and bylines'],
    ['radix-red', 'white', 'normal', 'accent text on white'],
    ['radix-red-deep', 'white', 'normal', 'eyebrows on white'],
    ['radix-dark', 'white', 'normal', 'headings'],

    // On the alternating surface
    ['ink', 'surface', 'normal', 'body copy on surface'],
    ['muted', 'surface', 'normal', 'secondary body on surface'],
    ['meta', 'surface', 'normal', 'captions on surface'],
    ['radix-red-deep', 'surface', 'normal', 'eyebrows on surface'],

    // Form controls sit on the raised panel, not the section surface
    ['placeholder', 'surface-raised', 'normal', 'unfilled form values'],
    ['ink', 'surface-raised', 'normal', 'filled form values'],

    // On dark sections
    ['on-dark', 'radix-dark', 'normal', 'body copy on dark'],
    ['on-dark-muted', 'radix-dark', 'normal', 'secondary copy on dark'],
    ['radix-red-on-dark', 'radix-dark', 'normal', 'eyebrows on dark'],
    ['radix-red-on-dark', 'radix-dark', 'large', 'large numerals on dark'],

    // On the red CTA band
    ['white', 'radix-red', 'normal', 'CTA band copy'],
    ['radix-red-deep', 'white', 'normal', 'inverse button label'],

    // Non-text boundaries
    ['line-control', 'white', 'ui', 'secondary button border'],
    ['line-control', 'surface', 'ui', 'form underline on surface'],
    ['radix-red', 'white', 'ui', 'focus ring'],
];

function parseTokens(css) {
    const tokens = { white: '#ffffff' };

    for (const [, name, value] of css.matchAll(/--color-([a-z0-9-]+):\s*(#[0-9a-fA-F]{3,8})\s*;/g)) {
        tokens[name] = value;
    }

    return tokens;
}

function channel(v) {
    const c = v / 255;

    return c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4;
}

function luminance(hex) {
    let h = hex.replace('#', '');

    if (h.length === 3) {
        h = h.split('').map((c) => c + c).join('');
    }

    const [r, g, b] = [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16));

    return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b);
}

function contrast(a, b) {
    const [l1, l2] = [luminance(a), luminance(b)].sort((x, y) => y - x);

    return (l1 + 0.05) / (l2 + 0.05);
}

const tokens = parseTokens(await readFile(CSS, 'utf8'));
const rows = [];
let failed = 0;

for (const [fg, bg, level, use] of PAIRS) {
    if (!tokens[fg] || !tokens[bg]) {
        console.error(`Unknown token in pair: ${fg} on ${bg}`);
        failed++;
        continue;
    }

    const ratio = contrast(tokens[fg], tokens[bg]);
    const needed = THRESHOLDS[level];
    const pass = ratio >= needed;

    if (!pass) {
        failed++;
    }

    rows.push({
        pair: `${fg} on ${bg}`,
        use,
        level,
        ratio: `${ratio.toFixed(2)}:1`,
        needs: `${needed}:1`,
        result: pass ? 'PASS' : 'FAIL',
    });
}

console.table(rows);

if (failed) {
    console.error(`\n${failed} contrast check(s) failed. Adjust the tokens in resources/css/app.css.`);
    process.exit(1);
}

console.log(`\nAll ${rows.length} contrast checks pass.`);
