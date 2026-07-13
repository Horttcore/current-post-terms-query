# Current Post Terms Query

A WordPress plugin that adds a **Show only terms of the current post** option to Terms Query blocks.

When enabled, a Terms Query block displays only terms assigned to the post currently being viewed. The editor preview and rendered block output use the same filtering behavior.

## Requirements

- WordPress 6.1 or later
- PHP 8.1 or later

## Installation

1. Download or clone this repository into `wp-content/plugins/current-post-terms-query`.
2. Install the plugin dependencies if you are building from source:

   ```sh
   npm install
   ```

3. Build the editor assets:

   ```sh
   npm run build
   ```

4. Activate **Current Post Terms Query** in WordPress.

## Usage

1. Add a Terms Query block to a template or post.
2. Select a taxonomy.
3. Enable **Show only terms of the current post** in the block settings.

If the Terms Query has configured included term IDs, the result is limited to the intersection of those IDs and the current post's terms.

## Development

Build the production editor bundle:

```sh
npm run build
```

Start the development watcher:

```sh
npm run start
```

The PHP filter is implemented in `src/CurrentPostTermsQueryFilter.php`. Editor controls and preview behavior are implemented in `src/js/editor/`.

## Author

[Ralf Hortt](mailto:mail@ralfhortt.dev)

## License

GPL-2.0-or-later. See [the GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html).
