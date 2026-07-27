/**
 * Pre-renders the two homepage maps to static inline SVG.
 *
 * The design concept loaded d3, topojson-client and a world-atlas topojson from
 * three separate CDNs, at runtime, inside an iframe — twice on the homepage. That
 * is roughly 250KB of JavaScript and three extra connections for what is, on this
 * page, decorative geography. This script does the projection work once, at build
 * time, and writes plain SVG path data into Blade components.
 *
 * Result: no runtime JS, no iframe, no external requests. The pin pulse is CSS.
 *
 * Run with `npm run build:maps`. Only needs re-running if the geography, the
 * served-market list, or the viewBox dimensions change — the output is committed,
 * so a normal build does not depend on this.
 *
 * The interactive dealer locator (Phase 5) is a different problem and will need a
 * real mapping provider; this is only the homepage's illustrative view.
 */
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { geoMercator, geoNaturalEarth1, geoPath } from 'd3-geo';
import { feature } from 'topojson-client';
import { presimplify, simplify, quantile } from 'topojson-simplify';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const OUT_DIR = resolve(ROOT, 'resources/views/components/map');

// Numeric ISO 3166-1 country codes for the markets Radix currently serves.
// Sourced from brief §4 + §11.5. Update here, not in the generated output.
const INDIA = 356;
const SERVED = new Set([566, 784, 4, 524, 64, 144, INDIA]); // NG, AE, AF, NP, BT, LK, IN

// Antarctica is a third of the world map's path data and carries no meaning for an
// export map. Dropping it is the single biggest saving in the generated SVG.
const ANTARCTICA = 10;

// Path coordinates are rounded to one decimal place. At the size these maps render
// the difference is invisible, and it roughly halves the inlined markup.
const PATH_DIGITS = 1;

const WORLD_PINS = [
    { name: 'India', coords: [78.9, 22.5] },
    { name: 'Nigeria', coords: [8.7, 9.1] },
    { name: 'UAE', coords: [54.3, 24.3] },
    { name: 'Afghanistan', coords: [66, 34] },
    { name: 'Nepal', coords: [84.2, 28.2] },
    { name: 'Bhutan', coords: [90.4, 27.4] },
    { name: 'Sri Lanka', coords: [80.7, 7.9] },
];

// Indicative distributor concentrations, not real dealer records. The genuine
// 650+ dealer dataset arrives from the client and drives the Phase 5 locator.
const INDIA_PINS = [
    [77.2, 28.6], [80.9, 26.8], [80.3, 26.4], [72.8, 19.1], [72.6, 23.0],
    [88.4, 22.6], [77.6, 13.0], [75.8, 26.9], [85.1, 25.6], [79.1, 21.1],
    [78.5, 17.4], [76.3, 9.9], [83.0, 25.3], [74.9, 30.9],
];

const round = (n) => Math.round(n * 100) / 100;

/**
 * The world map renders about 640px wide, where coastline detail from the source
 * data is far finer than a single pixel. Simplification is topology-preserving —
 * shared borders stay shared, so no seams open up between adjacent countries.
 *
 * `retain` is the share of coordinate points kept. India is loaded unsimplified
 * because it renders large and its outline is recognisable enough that a coarse
 * version reads as wrong.
 */
async function loadCountries({ retain } = {}) {
    const path = resolve(ROOT, 'node_modules/world-atlas/countries-110m.json');
    let topo = JSON.parse(await readFile(path, 'utf8'));

    if (retain) {
        topo = presimplify(topo);
        topo = simplify(topo, quantile(topo, retain));
    }

    return feature(topo, topo.objects.countries).features;
}

function pins(points, projection, radius) {
    return points
        .map((coords) => {
            const projected = projection(coords);

            // A projection given a bad input returns [NaN, NaN] rather than
            // throwing, which silently produces cx="NaN" in the output. Fail here
            // instead — a broken map should break the build, not ship quietly.
            if (!projected || !Number.isFinite(projected[0]) || !Number.isFinite(projected[1])) {
                throw new Error(`Could not project pin at ${JSON.stringify(coords)}`);
            }

            return projected;
        })
        .map(([x, y]) => {
            const cx = round(x);
            const cy = round(y);

            return (
                `        <circle class="radix-map__ping" cx="${cx}" cy="${cy}" r="${radius}" fill="currentColor" />\n` +
                `        <circle cx="${cx}" cy="${cy}" r="${radius - 0.5}" fill="currentColor" stroke="#fff" stroke-width="1.2" />`
            );
        })
        .join('\n');
}

function wrap({ width, height, body, title, description }) {
    return `{{--
    GENERATED FILE — do not edit by hand.
    Produced by scripts/build-maps.mjs (npm run build:maps).

    Colour comes from the parent's \`currentColor\`, so the same component works on
    light and dark sections. Decorative geography is set in --map-land.
--}}
<svg
    viewBox="0 0 ${width} ${height}"
    preserveAspectRatio="xMidYMid meet"
    role="img"
    aria-label="${title}"
    class="h-full w-full"
>
    <title>${title}</title>
    <desc>${description}</desc>
${body}
</svg>
`;
}

async function buildIndia(countries) {
    const width = 600;
    const height = 560;
    const india = countries.filter((d) => +d.id === INDIA);
    const projection = geoMercator().fitExtent(
        [[18, 18], [width - 18, height - 18]],
        { type: 'FeatureCollection', features: india }
    );
    const path = geoPath(projection).digits(PATH_DIGITS);

    const body =
        `    <g fill="currentColor" fill-opacity="0.16" stroke="currentColor" stroke-width="1">\n` +
        india.map((d) => `        <path d="${path(d)}" />`).join('\n') +
        `\n    </g>\n    <g>\n${pins(INDIA_PINS, projection, 4)}\n    </g>`;

    await writeFile(
        resolve(OUT_DIR, 'india.blade.php'),
        wrap({
            width,
            height,
            body,
            title: 'Map of India showing Radix distributor coverage',
            description:
                'Outline of India with markers indicating cities where Radix distributors operate. Indicative coverage, not a complete dealer list.',
        })
    );

    return india.length;
}

async function buildWorld(countries) {
    const width = 640;
    const height = 340;
    const mapped = countries.filter((d) => +d.id !== ANTARCTICA);
    const projection = geoNaturalEarth1().fitExtent(
        [[8, 8], [width - 8, height - 8]],
        { type: 'FeatureCollection', features: mapped }
    );
    const path = geoPath(projection).digits(PATH_DIGITS);

    const drawn = mapped.filter((d) => path(d));
    const other = drawn.filter((d) => !SERVED.has(+d.id));
    const served = drawn.filter((d) => SERVED.has(+d.id));

    const body =
        `    <g fill="var(--map-land, #d2dae4)" stroke="var(--map-line, #b3becc)" stroke-width="0.5">\n` +
        other.map((d) => `        <path d="${path(d)}" />`).join('\n') +
        `\n    </g>\n` +
        `    <g fill="currentColor" fill-opacity="0.9" stroke="currentColor" stroke-width="0.5">\n` +
        served.map((d) => `        <path d="${path(d)}" />`).join('\n') +
        `\n    </g>\n    <g>\n${pins(WORLD_PINS.map((p) => p.coords), projection, 4.5)}\n    </g>`;

    await writeFile(
        resolve(OUT_DIR, 'world.blade.php'),
        wrap({
            width,
            height,
            body,
            title: 'World map highlighting Radix export markets',
            description: `Countries Radix currently exports to are highlighted: ${WORLD_PINS.map((p) => p.name).join(', ')}.`,
        })
    );

    return served.length;
}

await mkdir(OUT_DIR, { recursive: true });

const [indiaPaths, servedPaths] = await Promise.all([
    buildIndia(await loadCountries()),
    buildWorld(await loadCountries({ retain: 0.35 })),
]);

console.log(
    `Maps written to resources/views/components/map/ ` +
    `(india: ${indiaPaths} path, world: ${servedPaths} served markets)`
);
