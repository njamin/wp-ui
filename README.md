# njamin/wp-ui

The shared UI layer of WordPress projects made from the blueprint: Alpine.js (CSP build) for
interactivity and the full Lucide icon set. Design tokens and UI primitives join it later
(blueprint P6).

- **Alpine**, CSP build, with its `focus` plugin, self-hosted and registered as `bp-alpine` (which
  loads `bp-alpine-focus` first). Both are deferred.
- **Lucide**, every icon of the release plus Lucide Lab's (sports and other extras, such as
  `bat-ball`), inlined server-side: `Icon::svg( 'lock' )`. A page carries only the icons it shows;
  they take the colour of their text.

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
