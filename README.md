# njamin/wp-ui

The shared UI layer of WordPress projects made from the blueprint: design tokens as CSS variables,
Alpine.js (CSP build) for interactivity and the full Lucide icon set. UI primitives and per-client
branding (`bp brand`) join it later (blueprint P6).

- **Tokens**, WordPress 7's design tokens as CSS variables (`--bp-color-*`, `--bp-tone-*`,
  `--bp-space-*`, `--bp-radius-*`, …), registered as `bp-tokens`.
- **Alpine**, CSP build, with its `focus` plugin, self-hosted and registered as `bp-alpine` (which
  loads `bp-alpine-focus` first). Both are deferred.
- **Lucide**, every icon of the release plus Lucide Lab's (sports and other extras, such as
  `bat-ball`), inlined server-side: `Icon::svg( 'lock' )`. A page carries only the icons it shows;
  they take the colour of their text.

## What belongs here

Every client project inherits this package, so it holds only what any client needs: semantic tokens
with neutral defaults, primitives and icons. Before adding something, place it:

- **Any client needs it** (a 16px field so phones do not zoom, a 48px touch target): here.
- **It is one client's look** (its colours, fonts, corner radius, type scale): that project's brand
  (`config/brand.json`), overriding the semantic tokens.
- **It belongs to one pattern** (a multi-step journey's width, its progress bar): the project's
  pattern stylesheet, as named values at its top, until a second client needs the same pattern
  (DESIGN-wordpress-blueprint §4.10.4, the rule of two). Then it is generalised and moves here.

A value copied from a client's current site or design is that client's, not a default.

## Requirements

WordPress 7.0 or later, PHP 8.3 or later, `composer/installers`.

## Install

```bash
composer require njamin/wp-ui
```

It installs as a must-use plugin folder (`wp-content/mu-plugins/wp-ui/`), so its scripts can be
served; WordPress doesn't load subfolders of `mu-plugins` by itself. Boot it from the project's
loader:

```php
\BP\UI\Plugin::boot();
```

## Tokens

`tokens.json` is the one source: colours, type, spacing (a 4px scale), control sizes, radii,
shadows, motion. `bin/generate-tokens.php` writes `assets/tokens.css` from it, which declares every
token as `--<name>` on `:root`. A stylesheet depends on the handle and uses the variables, never a
raw value:

```php
wp_register_style( 'acme-admin', $url, [ \BP\UI\Plugin::TOKENS ], $version );
```

```css
.acme-card { padding: var(--bp-space-6); background: var(--bp-color-surface); border-radius: var(--bp-radius-l); }
.acme-card:hover { transition: box-shadow var(--bp-duration-base) var(--bp-ease-out); }
```

- In wp-admin, `--bp-color-action` (and its hover and tint) follow the user's admin colour scheme.
- On a public page, fields use `--bp-text-body` (16px, so phones do not zoom) and
  `--bp-control-height-touch` (48px, for a finger).
- Under `prefers-reduced-motion`, every `--bp-duration-*` is `0s`, so motion built on them stops.
- A project's brand overrides a token in its own stylesheet (`--bp-font-display`), never edits the file.
- `tokens.json` uses the Design System artifact's format: a project's style guide reads it as is.

After editing `tokens.json`: `php bin/generate-tokens.php`.

## Alpine

Components are written for the CSP build: logic in `Alpine.data()`, never in attributes. A
component file registers on `alpine:init` and is printed in the footer without `defer`, so it runs
before Alpine starts:

```js
document.addEventListener( 'alpine:init', () => {
	Alpine.data( 'acmeFilters', () => ( { open: false, toggle() { this.open = ! this.open; } } ) );
} );
```

```php
wp_enqueue_script( 'acme-filters' ); // the component, no dependency on Alpine
wp_enqueue_script( \BP\UI\Plugin::ALPINE );
```

Put `x-cloak` on every component root.

## Icons

```php
echo \BP\UI\Icon::svg( 'lock' );                         // decorative, beside a word
echo \BP\UI\Icon::svg( 'filter', __( 'Filtres' ) );      // stands alone: an image with a name
add_menu_page( …, \BP\UI\Icon::data_uri( 'users' ) );   // admin menu icon
```

An unknown name throws, so a typo shows in tests. Names are Lucide's (lucide.dev/icons) and Lucide
Lab's (lucide.dev/icons/lab); where both have a name, the main set's icon is used.

## Updating

Alpine: replace the two files in `assets/lib/`, bump `Plugin::ALPINE_VERSION`. Lucide:

```bash
npm pack lucide-static@<version> @lucide/lab@<version>
mkdir core lab && tar -xzf lucide-static-*.tgz -C core && tar -xzf lucide-lab-*.tgz -C lab
php bin/generate-icons.php core/package lab/package
```

Licences of the bundled files: `assets/lib/LICENSES.md`.
